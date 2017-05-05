<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\ClassDiagramExtension;

use Dkplus\LivingDocs\Extension\Extension;
use Dkplus\LivingDocs\PagesExtension\PageType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ExprBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ClassDiagramExtension implements Extension, PageType
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'class diagram';
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
        return 'class_diagram';
    }

    public function validateAttributes(ExprBuilder $page): void
    {
        $page
            ->ifTrue(function (array $values) {
                return $values['type'] === 'class_diagram'
                    && (! isset($values['attributes']['definition'])
                        || ! is_string($values['attributes']['definition']));
            })
            ->thenInvalid('Each class_diagram needs a definition');
    }

    public function createPageDefinition(array $attributes): Definition
    {
        return new Definition(ClassDiagramPage::class, [$attributes['definition']]);
    }
}
