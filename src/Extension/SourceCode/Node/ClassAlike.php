<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\SourceTree\Node;

class ClassAlike implements Node
{
    /** @var string */
    private $name;

    /** @var string */
    private $package;

    /** @var string[] */
    private $implementations;

    /** @var string[] */
    private $extensions;

    /** @var string[] */
    private $associations;

    /**
     * @param string[] $implementations
     * @param string[] $extensions
     * @param string[] $associations
     */
    public function __construct(
        string $name,
        string $package,
        array $implementations,
        array $extensions,
        array $associations
    ) {
        $assertString = function (string $value) {
            return $value;
        };
        $this->name = $name;
        $this->package = $package;
        $this->implementations = array_map($assertString, $implementations);
        $this->extensions = array_map($assertString, $extensions);
        $this->associations = array_map($assertString, $associations);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function package(): string
    {
        return $this->package;
    }

    /** @return string[] */
    public function implementations(): array
    {
        return $this->implementations;
    }

    /** @return string[] */
    public function extensions(): array
    {
        return $this->extensions;
    }

    public function associations(): array
    {
        return $this->associations;
    }

    public function dependencies(): array
    {
    }

    public function creations(): array
    {
    }
}
