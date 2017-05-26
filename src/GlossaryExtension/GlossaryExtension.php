<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\GlossaryExtension;

use Dkplus\LivingDocumentation\Extension\Extension;
use Dkplus\LivingDocumentation\PagesExtension\PageType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ExprBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class GlossaryExtension implements Extension, PageType
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'glossary';
    }

    public function configure(ArrayNodeDefinition $builder, array $extensions): void
    {
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');
    }

    public function getPageTypeIdentifier(): string
    {
        return 'glossary';
    }

    public function validateAttributes(ExprBuilder $page): void
    {
        $page->ifTrue(function (array $values) {
            return $values['type'] === 'glossary'
                && ! isset($values['attributes']['title']);
        })->thenInvalid('Each glossary page needs a title');
    }

    public function createPageDefinition(array $attributes): Definition
    {
        return new Definition(GlossaryPage::class, [$attributes['title']]);
    }
}
