<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use function array_column;

class CoreExtension implements Extension
{
    public function process(ContainerBuilder $container): void
    {
        $processingStepsWithPriority = [];
        $services = $container->findTaggedServiceIds('dkplus.processing_step');
        /* @var $tags array */
        foreach ($services as $id => $tags) {
            /* @var $tag array */
            foreach ($tags as $tag) {
                $processingStepsWithPriority[] = ['id' => $id, 'priority' => $tag['priority'] ?? -1];
            }
        }
        usort($processingStepsWithPriority, function (array $stepOne, array $stepTwo) {
            return $stepTwo['priority'] <=> $stepOne['priority'];
        });
        $orderedProcessingSteps = array_column($processingStepsWithPriority, 'id');
        $processor = $container->getDefinition('dkplus.processor');
        foreach ($orderedProcessingSteps as $eachId) {
            $processor->addMethodCall('addStep', [new Reference($eachId)]);
        }
    }

    public function getConfigKey(): string
    {
        return 'dkplus';
    }

    public function configure(ArrayNodeDefinition $builder, array $extensions): void
    {
        $builder->children()->arrayNode('sources')->prototype('scalar');
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('core.xml');
    }
}
