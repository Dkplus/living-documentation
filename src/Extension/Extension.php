<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface Extension extends CompilerPassInterface
{
    public function getConfigKey(): string;

    /**
     * @param ArrayNodeDefinition $builder
     * @param Extension[] $extensions
     */
    public function configure(ArrayNodeDefinition $builder, array $extensions): void;

    public function load(ContainerBuilder $container, array $config): void;
}
