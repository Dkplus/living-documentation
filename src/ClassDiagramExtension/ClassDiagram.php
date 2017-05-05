<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\ClassDiagramExtension;

use Assert\Assert;
use Dkplus\LivingDocs\SightseeingExtension\ClassPointOfInterestDescription;
use Dkplus\LivingDocs\SightseeingExtension\PointOfInterest;
use Dkplus\LivingDocs\SightseeingExtension\PointOfInterestDescription;
use InvalidArgumentException;
use const ARRAY_FILTER_USE_BOTH;
use function array_combine;
use function array_diff;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_search;
use function array_unshift;
use function array_values;
use function explode;
use function implode;
use function in_array;
use function var_dump;

class ClassDiagram
{
    /** @var string[] */
    private $classes = [];

    /** @var string[] */
    private $interfaces = [];

    /** @var string[] */
    private $abstractClasses = [];

    /** @var string[] */
    private $externalActors = [];

    /** @var string[] */
    private $allClasses = [];

    /** @var string[][][] */
    private $dependencies = [];

    /** @var string[][] */
    private $associations = [];

    /** @var string[][] */
    private $implementations = [];

    /** @var string[][] */
    private $generalizations = [];

    /** @var string[][] */
    private $compositions = [];

    /** @var string[][] */
    private $aggregations = [];

    /** @var string[] */
    private $classToShortClassMap = [];

    /** @var string[] */
    private $shortClassToClassMap = [];

    /** @var string[][] */
    private $filteredDependencies = [];

    /** @var string[][] */
    private $filteredAssociations = [];

    public function abbreviateClassNames(): void
    {
        $this->classToShortClassMap = [];
        $this->shortClassToClassMap = [];

        foreach ($this->allClasses as $each) {
            $otherClasses = array_diff($this->allClasses, [$each]);
            $parts = explode('\\', $each);
            $chosenParts = [];
            do {
                array_unshift($chosenParts, array_pop($parts));
                $currentClassName = implode('\\', $chosenParts);
                $otherClasses = array_filter($otherClasses, function (string $className) use ($currentClassName) {
                    return mb_substr($className, -1 * mb_strlen($currentClassName) - 1) === "\\$currentClassName";
                });
            } while (count($otherClasses) > 0);
            $this->classToShortClassMap[$each] = $currentClassName;
            $this->shortClassToClassMap[$currentClassName] = $each;
        }
    }

    public function removeRedundantArrows(): void
    {
        foreach ($this->allClasses as $eachClass) {
            $baseClasses = array_merge(
                $this->implementations[$eachClass] ?? [],
                $this->generalizations[$eachClass] ?? []
            );
            foreach ($this->associations[$eachClass] ?? [] as $association => $multiplicity) {
                foreach ($baseClasses as $eachBase) {
                    if (array_key_exists($association, $this->associations[$eachBase] ?? [])
                        && $this->associations[$eachBase][$association] === $multiplicity
                    ) {
                        $this->filteredAssociations[$eachClass][$association] = $multiplicity;
                        break;
                    }
                }
            }
            /* @var $itsLabels string[] */
            foreach ($this->dependencies[$eachClass] ?? [] as $eachDependency => $itsLabels) {
                if (count($itsLabels) > 1 && in_array('', $itsLabels, true)) {
                    $this->filteredDependencies[$eachClass][$eachDependency][] = '';
                }
                foreach ($baseClasses as $eachBase) {
                    if (array_key_exists($eachDependency, $this->dependencies[$eachBase] ?? [])) {
                        $this->filteredDependencies[$eachClass][$eachDependency] = array_merge(
                            $this->filteredDependencies[$eachClass][$eachDependency] ?? [],
                            array_intersect(
                                $this->dependencies[$eachBase][$eachDependency],
                                $this->dependencies[$eachClass][$eachDependency]
                            )
                        );
                    }
                }
            }
        }
    }

    public function replaceDefaultDependencyLabel(string $class, string $dependentClass, string $label): void
    {
        if (! isset($this->dependencies[$class][$dependentClass])) {
            throw new InvalidArgumentException("There is no dependency from $class to $dependentClass");
        }
        foreach ($this->generalizations as $eachClass => $itsParents) {
            if (in_array($class, $itsParents)) {
                try {
                    $this->replaceDefaultDependencyLabel($eachClass, $dependentClass, $label);
                } catch (InvalidArgumentException $exception) {
                }
            }
        }
        foreach ($this->implementations as $eachClass => $itsParents) {
            if (in_array($class, $itsParents)) {
                try {
                    $this->replaceDefaultDependencyLabel($eachClass, $dependentClass, $label);
                } catch (InvalidArgumentException $exception) {
                }
            }
        }
        if (($position = array_search('', $this->dependencies[$class][$dependentClass])) !== false) {
            unset($this->dependencies[$class][$dependentClass][$position]);
            if (! in_array($label, $this->dependencies[$class][$dependentClass])) {
                $this->dependencies[$class][$dependentClass][] = $label;
            }
        }
    }

    public function addInterface(string $interface): void
    {
        $this->allClasses[] = $interface;
        $this->interfaces[] = $interface;

        $this->classToShortClassMap[$interface] = $interface;
        $this->shortClassToClassMap[$interface] = $interface;
    }

    public function addClass(string $class): void
    {
        $this->classes[] = $class;
        $this->allClasses[] = $class;

        $this->classToShortClassMap[$class] = $class;
        $this->shortClassToClassMap[$class] = $class;
    }

    public function addAbstractClass(string $class): void
    {
        $this->abstractClasses[] = $class;
        $this->allClasses[] = $class;

        $this->classToShortClassMap[$class] = $class;
        $this->shortClassToClassMap[$class] = $class;
    }

    public function addExternalActor(string $actor): void
    {
        $this->externalActors[] = $actor;
        $this->allClasses[] = $actor;

        $this->classToShortClassMap[$actor] = $actor;
        $this->shortClassToClassMap[$actor] = $actor;
    }

    public function getAllClasses(): array
    {
        return array_values($this->classToShortClassMap);
    }

    public function getClasses(): array
    {
        return array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, $this->classes);
    }

    public function getAbstractClasses(): array
    {
        return array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, $this->abstractClasses);
    }

    public function getInterfaces(): array
    {
        return array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, $this->interfaces);
    }

    public function getExternalActors(): array
    {
        return array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, $this->externalActors);
    }

    public function addDependency(string $class, string $dependentOn, string $label): void
    {
        Assert::that($class)->inArray($this->allClasses);
        Assert::that($dependentOn)->inArray($this->allClasses);
        if (! in_array($label, $this->dependencies[$class][$dependentOn] ?? [])) {
            $this->dependencies[$class][$dependentOn][] = $label;
        }
    }

    public function getDependencies(string $class): array
    {
        $associations = $this->getAssociations($class);

        $class = $this->shortClassToClassMap[$class] ?? $class;
        $dependencies = array_keys($this->dependencies[$class] ?? []);
        $dependencies = array_combine($dependencies, $dependencies);
        $dependencies = array_map(function (string $eachDependency) use ($class) {
            $labels = $this->dependencies[$class][$eachDependency];
            $filteredLabels = $this->filteredDependencies[$class][$eachDependency] ?? [];
            return array_diff($labels, $filteredLabels);
        }, array_combine($dependencies, $dependencies));

        $dependencies = array_combine(array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, array_keys($dependencies)), $dependencies);

        foreach ($dependencies as $eachClass => $itsLabels) {
            if (array_key_exists($eachClass, $associations)) {
                $dependencies[$eachClass] = array_diff($itsLabels, ['']);
            }
        }
        return $dependencies;
    }

    public function addAssociation(string $class, string $associated, string $multiply): void
    {
        Assert::that($class)->inArray($this->allClasses);
        Assert::that($associated)->inArray($this->allClasses);
        $this->associations[$class][$associated] = $multiply;
    }

    public function getAssociations(string $class): array
    {
        $class = $this->shortClassToClassMap[$class] ?? $class;
        $associations = $this->associations[$class] ?? [];
        $associations = array_filter($associations, function (string $multiplicity, string $association) use ($class) {
            return ! array_key_exists($association, $this->filteredAssociations[$class] ?? [])
                || $this->filteredAssociations[$class][$association] !== $multiplicity;
        }, ARRAY_FILTER_USE_BOTH);
        return array_combine(array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, array_keys($associations)), $associations);
    }

    public function addGeneralization(string $class, string $extendedClass): void
    {
        Assert::that($class)->inArray($this->allClasses);
        Assert::that($extendedClass)->inArray($this->allClasses);
        $this->generalizations[$class][] = $extendedClass;
    }

    public function getGeneralizations(string $class): array
    {
        $class = $this->shortClassToClassMap[$class] ?? $class;
        $usages = $this->generalizations[$class] ?? [];
        return array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, $usages);
    }

    public function addImplementation(string $class, string $implementedClass): void
    {
        Assert::that($class)->inArray($this->allClasses);
        Assert::that($implementedClass)->inArray($this->allClasses);
        $this->implementations[$class][] = $implementedClass;
    }

    public function getImplementations(string $class): array
    {
        $class = $this->shortClassToClassMap[$class] ?? $class;
        $implementations = $this->implementations[$class] ?? [];
        return array_map(function (string $class) {
            return $this->classToShortClassMap[$class];
        }, $implementations);
    }

    public function addComposition(string $class, string $composition): void
    {
        Assert::that($class)->inArray($this->allClasses);
        Assert::that($composition)->inArray($this->allClasses);
        $this->compositions[$class][] = $composition;
    }

    public function addAggregation(string $class, string $aggregate): void
    {
        Assert::that($class)->inArray($this->allClasses);
        Assert::that($aggregate)->inArray($this->allClasses);
        $this->aggregations[$class][] = $aggregate;
    }
}
