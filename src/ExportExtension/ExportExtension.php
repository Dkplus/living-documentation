<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\ExportExtension;

use Dkplus\LivingDocumentation\Extension\Extension;
use RuntimeException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ExportExtension implements Extension
{
    public function process(ContainerBuilder $container): void
    {
        $processing = $container->getDefinition('dkplus.export.processing');

        /* @var $pageRenderers array[] */
        $pageRenderers = $container->findTaggedServiceIds('dkplus.export.page_renderer');
        $pageRenderersByName = [];
        foreach ($pageRenderers as $eachId => $eachTags) {
            foreach ($eachTags as $eachAttributes) {
                if (! isset($eachAttributes['id'])) {
                    throw new RuntimeException(
                        "Each service tagged with 'dkplus.export.page_renderer' "
                        . "must have a id attribute, $eachId has not"
                    );
                }
                $pageRenderersByName[$eachAttributes['id']] = $eachId;
            }
        }

        /* @var $exports array[] */
        $exports = $container->findTaggedServiceIds('dkplus.export.export');
        foreach ($exports as $eachId => $eachTags) {
            foreach ($eachTags as $eachAttributes) {
                if (! isset($eachAttributes['page_renderer'])) {
                    throw new RuntimeException(
                        "Each service tagged with 'dkplus.export.export' "
                        . "must have a page_renderer attribute, $eachId does not"
                    );
                }

                $pageRenderer = $eachAttributes['page_renderer'];
                if (! isset($pageRenderersByName[$pageRenderer])) {
                    throw new RuntimeException(
                        "Service $eachId requires a 'dkplus.export.page_renderer' with name $pageRenderer, "
                        . 'but such one does not exist'
                    );
                }
                $rendererId = $pageRenderersByName[$pageRenderer];

                $processing->addMethodCall(
                    'addExport',
                    [new Reference($eachId), new Reference($rendererId)]
                );
            }
        }
    }

    public function getConfigKey(): string
    {
        return 'exports';
    }

    public function configure(ArrayNodeDefinition $builder, array $extensions): void
    {
        $eachExport = $builder->useAttributeAsKey('title')->prototype('array')->children();
        $eachExport->scalarNode('target')->isRequired()->cannotBeEmpty();
        $eachExport->scalarNode('renderer')->isRequired()->cannotBeEmpty();
        $eachExport->arrayNode('pages')->isRequired()->cannotBeEmpty()->prototype('scalar')->cannotBeEmpty();

        /* @var $eachMenu ArrayNodeDefinition */
        $eachMenu = $eachExport->arrayNode('menu')->prototype('array');
        $eachMenu->beforeNormalization()->ifString()->then(function (string $value) {
            return ['page' => $value];
        });
        $eachMenu->children()->scalarNode('page')->cannotBeEmpty();
        $eachMenu->children()->arrayNode('children')->defaultValue([])->prototype('scalar')->cannotBeEmpty();
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');

        foreach ($config as $title => $export) {
            $eachDefinition = new Definition(
                Export::class,
                [
                    $export['target'],
                    $export['pages'],
                    $export['menu'],
                    ['title' => $title],
                ]
            );
            $eachDefinition->addTag('dkplus.export.export', ['page_renderer' => $export['renderer']]);
            $container->addDefinitions(['dkplus.export._' . $title => $eachDefinition]);
        }
    }
}
