<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\SourceTree\Node;

class ClassAlike implements Node
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function package(): string
    {
    }

    public function implementations(): array
    {
    }

    public function extensions(): array
    {
    }

    public function associations(): array
    {
    }

    public function dependencies(): array
    {
    }

    public function creations(): array
    {
    }
}
