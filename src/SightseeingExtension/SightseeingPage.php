<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SightseeingExtension;

use Dkplus\LivingDocs\PagesExtension\Page;

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
