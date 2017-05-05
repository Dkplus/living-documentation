<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension;

use Dkplus\LivingDocs\Extension\Extension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use function array_keys;
use function array_map;

class SourceCodeExtension implements Extension
{
    public function process(ContainerBuilder $container): void
    {
        $codeIterator = $container->getDefinition('dkplus.source_code.code_iterator');
        $codeListeners = $container->findTaggedServiceIds('dkplus.source_code.code_listener');
        foreach (array_keys($codeListeners) as $id) {
            $codeIterator->addMethodCall('addListener', [new Reference($id)]);
        }

        $annotationIterator = $container->getDefinition('dkplus.source_code.annotation_iterator');
        $annotationSubscribers = $container->findTaggedServiceIds('dkplus.source_code.annotation_subscriber');
        foreach (array_keys($annotationSubscribers) as $id) {
            $annotationIterator->addMethodCall('registerSubscriber', [new Reference($id)]);
        }
    }

    public function getConfigKey(): string
    {
        return 'source code';
    }

    public function configure(ArrayNodeDefinition $builder, array $extensions): void
    {
        $children = $builder->children();
        $children->arrayNode('directories')->prototype('scalar')->cannotBeEmpty();
        $children->booleanNode('annotations')->defaultTrue();
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $basePath = $container->getParameter('paths.base');
        $sourcePaths = array_map(function (string $path) use ($basePath) {
            return $basePath . '/' . $path;
        }, $config['directories']);
        $container->setParameter('source_code.directories', $sourcePaths);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');

        if ($config['annotations']) {
            $container
                ->getDefinition('dkplus.source_code.code_iterator')
                ->addMethodCall('addListener', [new Reference('dkplus.source_code.annotation_iterator')]);
        }
    }
}
