<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

use Dkplus\LivingDocs\Extension\Extension;
use RuntimeException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use function array_diff_key;
use function array_keys;
use function implode;
use function var_dump;

class PagesExtension implements Extension
{
    /** @var PageType[] */
    private $pageTypes = [];

    public function process(ContainerBuilder $container): void
    {
        $processingStep = $container->getDefinition('dkplus.pages.processing');

        /* @var $processors array[] */
        $processors = $container->findTaggedServiceIds('dkplus.pages.processor');
        $processorIdsByType = [];
        foreach ($processors as $eachId => $eachTags) {
            foreach ($eachTags as $eachAttributes) {
                if (! isset($eachAttributes['page_type'])) {
                    throw new RuntimeException(
                        "Each service tagged with 'dkplus.pages.processor' "
                        . "must have a page_type attribute, $eachId does not"
                    );
                }
                $processorIdsByType[$eachAttributes['page_type']] = $eachId;
            }
        }

        /* @var $pages array[] */
        $pages = $container->findTaggedServiceIds('dkplus.pages.page');
        foreach ($pages as $eachId => $eachTags) {
            foreach ($eachTags as $eachAttributes) {
                if (! isset($eachAttributes['page_type'])) {
                    throw new RuntimeException(
                        "Each service tagged with 'dkplus.pages.page' "
                        . "must have a page_type attribute, $eachId does not"
                    );
                }

                if (! isset($eachAttributes['page_id'])) {
                    throw new RuntimeException(
                        "Each service tagged with 'dkplus.pages.page' "
                        . "must have a page_id attribute, $eachId does not"
                    );
                }

                $pageType = $eachAttributes['page_type'];
                if (! isset($processorIdsByType[$pageType])) {
                    throw new RuntimeException(
                        "Service $eachId requires a 'dkplus.pages.processor' with page_type $pageType, "
                        . 'but such one does not exist'
                    );
                }
                $processorId = $processorIdsByType[$pageType];

                $processingStep->addMethodCall(
                    'addPage',
                    [$eachAttributes['page_id'], new Reference($eachId), new Reference($processorId)]
                );
            }
        }
    }

    public function getConfigKey(): string
    {
        return 'pages';
    }

    public function configure(ArrayNodeDefinition $builder, array $extensions): void
    {
        foreach ($extensions as $each) {
            if ($each instanceof PageType) {
                $this->pageTypes[$each->getPageTypeIdentifier()] = $each;
            }
        }

        $builder->normalizeKeys(false);
        $eachPage = $builder->prototype('array');
        $eachPage
            ->beforeNormalization()
            ->ifTrue(function ($values) {
                return is_array($values)
                    && array_keys($values) !== ['type', 'attributes'];
            })->then(function (array $values) {
                $result = ['attributes' => array_diff_key($values, ['type' => ''])];
                if (isset($values['type'])) {
                    $result['type'] = $values['type'];
                }
                return $result;
            });
        $eachPage
            ->children()
            ->scalarNode('type')
            ->isRequired()
            ->cannotBeEmpty()
            ->validate()
            ->ifNotInArray(array_keys($this->pageTypes))
            ->thenInvalid('Type must be one of ' . implode(', ', array_keys($this->pageTypes)));
        $eachPage
            ->children()
            ->arrayNode('attributes')
            ->prototype('variable')
            ->isRequired();

        $eachPageValidator = $eachPage->validate();
        foreach ($this->pageTypes as $each) {
            $each->validateAttributes($eachPageValidator);
        }
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');

        $definitions = [];
        foreach ($config as $eachId => $eachPage) {
            $pageType = $this->pageTypes[$eachPage['type']];
            $definitions['dkplus.pages._' . $eachId] = $pageType
                    ->createPageDefinition($eachPage['attributes'])
                    ->addTag('dkplus.pages.page', ['page_type' => $eachPage['type'], 'page_id' => $eachId]);
        }
        $container->addDefinitions($definitions);
    }
}
