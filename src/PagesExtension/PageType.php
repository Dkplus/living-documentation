<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

use Symfony\Component\Config\Definition\Builder\ExprBuilder;
use Symfony\Component\DependencyInjection\Definition;

interface PageType
{
    public function getPageTypeIdentifier(): string;
    public function validateAttributes(ExprBuilder $page): void;
    public function createPageDefinition(array $attributes): Definition;
}
