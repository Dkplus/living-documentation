<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\MarkdownExtension;

use Dkplus\LivingDocs\Extension\Extension;
use Dkplus\LivingDocs\PagesExtension\PageType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ExprBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class MarkdownExtension implements Extension, PageType
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'markdown';
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
        return 'markdown';
    }

    public function validateAttributes(ExprBuilder $page): void
    {
        $page->ifTrue(function (array $values) {
            return $values['type'] === $this->getPageTypeIdentifier()
                && ! isset($values['attributes']['source']);
        })->thenInvalid('Each markdown needs a source');
        $page->ifTrue(function (array $values) {
            return $values['type'] === $this->getPageTypeIdentifier()
                && ! isset($values['attributes']['title']);
        })->thenInvalid('Each markdown needs a title');
    }

    public function createPageDefinition(array $attributes): Definition
    {
        return new Definition(MarkdownPage::class, [$attributes['title'], $attributes['source']]);
    }
}
