<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\ClassDiagramExtension;

use RuntimeException;
use function array_filter;
use function array_walk;

class ClassDiagramDescription
{
    /** @var string */
    private $title;

    /** @var string[] */
    private $onTheFlyClasses = [];

    /** @var string[][] */
    private $onTheFlyDependencies = [];

    /** @var string[][] */
    private $onTheFlyAssociations = [];

    /** @var string[][] */
    private $onTheFlyExtensions = [];

    /** @var string[][] */
    private $onTheFlyImplementations = [];

    /** @var ?string */
    private $lastOnTheFly;

    /** @var array */
    private $classes;

    /** @var string[][] */
    private $dependencyLabels = [];

    public static function ofClasses(string $title, array $classes): self
    {
        return new self($title, $classes);
    }

    public function addExternalActor(string $actor): self
    {
        $this->onTheFlyClasses[] = ['type' => 'external', 'name' => $actor];
        $this->lastOnTheFly = $actor;
        return $this;
    }

    public function addClass(string $class): self
    {
        $this->onTheFlyClasses[] = ['type' => 'class', 'name' => $class];
        $this->lastOnTheFly = $class;
        return $this;
    }

    public function addInterface(string $class): self
    {
        $this->onTheFlyClasses[] = ['type' => 'interface', 'name' => $class];
        $this->lastOnTheFly = $class;
        return $this;
    }

    public function addAbstractClass(string $class): self
    {
        $this->onTheFlyClasses[] = ['type' => 'abstract', 'name' => $class];
        $this->lastOnTheFly = $class;
        return $this;
    }

    public function thatDependsOn(string $class, string $label): self
    {
        if (! $this->lastOnTheFly) {
            throw new RuntimeException("Who depends on $class?");
        }
        $this->onTheFlyDependencies[$this->lastOnTheFly][$class] = $label;
        return $this;
    }

    public function thatHasAnAssociationTo(string $class, string $label): self
    {
        if (! $this->lastOnTheFly) {
            throw new RuntimeException("Who depends on $class?");
        }
        $this->onTheFlyAssociations[$this->lastOnTheFly][$class] = $label;
        return $this;
    }

    public function thatImplements(string $class): self
    {
        if (! $this->lastOnTheFly) {
            throw new RuntimeException("Who implements $class?");
        }
        $this->onTheFlyImplementations[$this->lastOnTheFly][] = $class;
        return $this;
    }

    public function thatExtends(string $class): self
    {
        if (! $this->lastOnTheFly) {
            throw new RuntimeException("Who extends $class?");
        }
        $this->onTheFlyExtensions[$this->lastOnTheFly][] = $class;
        return $this;
    }

    public function labelDependency(string $oneDependency, string $anotherDependency, string $label): self
    {
        $this->dependencyLabels[$oneDependency][$anotherDependency] = $label;
        return $this;
    }

    private function __construct(string $title, array $classes)
    {
        $this->title = $title;
        $this->classes = $classes;
    }

    /** @return string */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function addPredefinedToDiagram(ClassDiagram $diagram): void
    {
        $typesToMethod = [
            'class' => 'addClass',
            'external' => 'addExternalActor',
            'interface' => 'addInterface',
            'abstract' => 'addAbstractClass',
        ];
        foreach ($typesToMethod as $type => $method) {
            $allOfEachType = array_column(array_filter($this->onTheFlyClasses, function (array $class) use ($type) {
                return $class['type'] === $type;
            }), 'name');
            array_walk($allOfEachType, [$diagram, $method]);
        }

        foreach ($this->onTheFlyAssociations as $eachClass => $itsAssociations) {
            foreach ($itsAssociations as $eachAssociation => $itsLabel) {
                $diagram->addAssociation($eachClass, $eachAssociation, $itsLabel);
            }
        }
        foreach ($this->onTheFlyDependencies as $eachClass => $itsDependencies) {
            foreach ($itsDependencies as $eachDependency => $itsLabel) {
                $diagram->addDependency($eachClass, $eachDependency, $itsLabel);
            }
        }
        foreach ($this->onTheFlyExtensions as $eachClass => $itsExtensions) {
            foreach ($itsExtensions as $eachExtension) {
                $diagram->addGeneralization($eachClass, $eachExtension);
            }
        }
        foreach ($this->onTheFlyImplementations as $eachClass => $itsImplementations) {
            foreach ($itsImplementations as $eachImplementation) {
                $diagram->addImplementation($eachClass, $eachImplementation);
            }
        }
        foreach ($this->dependencyLabels as $eachClass => $itsDependencies) {
            foreach ($itsDependencies as $eachDependency => $itsLabel) {
                $diagram->replaceDefaultDependencyLabel($eachClass, $eachDependency, $itsLabel);
            }
        }
    }

    /** @return array */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
