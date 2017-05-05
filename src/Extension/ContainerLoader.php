<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Extension;

use function array_diff;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ContainerLoader
{
    /** @var ExtensionManager */
    private $extensionManager;

    public function __construct(ExtensionManager $extensionManager)
    {
        $this->extensionManager = $extensionManager;
    }

    public function load(ContainerBuilder $container, array $configs): void
    {
        $extensionClasses = $configs['extensions'] ?? [];
        unset($configs['extensions']);
        $this->extensionManager->addExtensionsByClassName($extensionClasses);
        $extensions = $this->extensionManager->instantiateExtensions();

        $configTree = new TreeBuilder();
        $configChildren = $configTree->root('dkplus')->children();
        foreach ($extensions as $each) {
            $each->configure($configChildren->arrayNode($each->getConfigKey()), $extensions);
        }
        $config = (new Processor())->process($configTree->buildTree(), ['dkplus' => $configs]);
        foreach ($extensions as $each) {
            $eachContainer = new ContainerBuilder(new ParameterBag($container->getParameterBag()->all()));
            $each->load($eachContainer, $config[$each->getConfigKey()] ?? []);
            $container->merge($eachContainer);
        }
        foreach ($extensions as $each) {
            $container->addCompilerPass($each);
        }
    }
}
