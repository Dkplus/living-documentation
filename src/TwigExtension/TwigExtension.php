<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\TwigExtension;

use Dkplus\LivingDocumentation\Extension\Extension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class TwigExtension implements Extension
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'twig';
    }

    public function configure(ArrayNodeDefinition $builder, array $extensions): void
    {
        $builder->children()->scalarNode('template_dir')->isRequired()->cannotBeEmpty();
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');

        $container->setParameter('twig.template_dir', $config['template_dir']);
    }
}
