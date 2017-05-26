<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SightseeingExtension;

use Dkplus\LivingDocumentation\PagesExtension\Page;

class SightseeingPage implements Page
{
    /** @var string */
    private $definitionPath;

    public function __construct(string $definitionPath)
    {
        $this->definitionPath = $definitionPath;
    }

    public function getDefinitionPath(): string
    {
        return $this->definitionPath;
    }
}
