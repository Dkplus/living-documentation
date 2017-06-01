<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection;

use function array_merge;
use function array_unique;
use ReflectionClass;

abstract class StatefulClassAlike extends ClassAlike
{
    /** @var Property[] */
    private $properties;

    /** @var Trait_[] */
    private $traits;

    public function __construct(ReflectionClass $reflection, array $traits, array $properties, array $methods)
    {
        parent::__construct($reflection, $methods);
        $this->properties = $properties;
        $this->traits = $traits;
    }

    public function properties(): array
    {
    }

    public function property(string $name): Property
    {
    }

    /** @return Trait_[] */
    public function explicitUsedTraits(): array
    {
        return $this->traits;
    }

    public function usedTraits(): array
    {
        $traits = $this->traits;
        foreach ($this->traits as $eachTrait) {
            $traits = array_merge($traits, $eachTrait->usedTraits());
        }
        return array_unique($traits);
    }
}
