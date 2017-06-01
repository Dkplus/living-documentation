<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection;

use ReflectionClass;
use RuntimeException;
use function array_merge;
use function array_unique;

class Class_ extends StatefulClassAlike
{
    /** @var Class_|null */
    private $parentClass;

    /** @var array */
    private $implementedInterfaces;

    public function __construct(
        ReflectionClass $reflection,
        ?Class_ $parentClass,
        string $dockBlock,
        array $implementedInterfaces,
        array $usedTraits,
        array $properties,
        array $methods
    ) {
        parent::__construct($reflection, $usedTraits, $properties, $methods);
        $this->parentClass = $parentClass;
        $this->implementedInterfaces = $implementedInterfaces;
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function hasExplicitParentClass(): bool
    {
        return $this->parentClass !== null;
    }

    public function explicitParentClass(): Class_
    {
        if (! $this->parentClass) {
            throw new RuntimeException();
        }
        return $this->parentClass;
    }

    /** @return Class_[] */
    public function parentClasses(): array
    {
        $result = [];
        $parent = $this->parentClass;
        while ($parent) {
            $result[] = $parent;
            $parent = $parent->parentClass;
        }
        return $result;
    }

    /** @return Interface_[] */
    public function explicitImplementedInterfaces(): array
    {
        return $this->implementedInterfaces;
    }

    /** @return Interface_[] */
    public function implementedInterfaces(): array
    {
        $interfaces = $this->implementedInterfaces;
        foreach ($this->implementedInterfaces() as $each) {
            $interfaces = array_merge($interfaces, $each->extendedInterfaces());
        }
        if ($this->hasExplicitParentClass()) {
            $interfaces = array_merge($interfaces, $this->parentClass->implementedInterfaces());
        }
        return array_unique($interfaces);
    }

    /** @return Trait_[] */
    public function usedTraits(): array
    {
        $traits = parent::usedTraits();
        foreach ($this->parentClasses() as $eachParent) {
            $traits[] = array_merge($eachParent->usedTraits());
        }
        return array_unique($traits);
    }
}
