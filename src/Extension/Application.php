<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Application extends BaseApplication
{
    /** @var ConfigurationLoader */
    private $configLoader;

    /** @var ExtensionManager */
    private $extensionManager;

    public function __construct(
        string $name,
        string $version,
        ConfigurationLoader $configLoader,
        ExtensionManager $extensionManager
    ) {
        parent::__construct($name, $version);
        $this->configLoader = $configLoader;
        $this->extensionManager = $extensionManager;
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption(
            '--config',
            '-c',
            InputOption::VALUE_OPTIONAL,
            'Specify config file to use.'
        ));
        return $definition;
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasParameterOption(['--config', '-c'])) {
            $this->configLoader->setConfigurationFilePath($input->getParameterOption(['--config', '-c']));
        }
        $this->add($this->createCommand($input, $output));
        return parent::doRun($input, $output);
    }

    protected function getCommandName(InputInterface $input): string
    {
        return $this->getName();
    }

    private function createCommand(InputInterface $input, OutputInterface $output): Command
    {
        return $this->createContainer($input, $output)->get('cli.command');
    }

    private function createContainer(InputInterface $input, OutputInterface $output): ContainerBuilder
    {
        $basePath = rtrim($this->configLoader->getBasePath(), DIRECTORY_SEPARATOR);

        $container = new ContainerBuilder();
        $container->setParameter('cli.command.name', $this->getName());
        $container->setParameter('paths.base', $basePath);
        $container->set('cli.input', $input);
        $container->set('cli.output', $output);

        if (! $input->hasParameterOption(['--help', '-h'])) {
            $extension = new ContainerLoader($this->extensionManager);
            $extension->load($container, $this->configLoader->loadConfiguration());
            $container->addObjectResource($extension);
        }
        $container->compile();
        return $container;
    }
}
