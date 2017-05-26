<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\ClassDiagramExtension;

use Dkplus\LivingDocumentation\PagesExtension\Page;

class ClassDiagramPage implements Page
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
