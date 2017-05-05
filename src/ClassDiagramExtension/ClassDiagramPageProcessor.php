<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\ClassDiagramExtension;

use Dkplus\LivingDocs\Annotation\FactoryMethod;
use Dkplus\LivingDocs\PagesExtension\Page;
use Dkplus\LivingDocs\PagesExtension\PageProcessor;
use Dkplus\LivingDocs\PagesExtension\ProcessedPage;
use Dkplus\LivingDocs\SourceCodeExtension\AnnotationListeners;
use Dkplus\LivingDocs\SourceCodeExtension\ClassDependencies;
use Dkplus\LivingDocs\SourceCodeExtension\Listener\AnnotationSubscriber;
use Dkplus\LivingDocs\SourceCodeExtension\Packages;
use InvalidArgumentException;
use ReflectionMethod;
use const ARRAY_FILTER_USE_KEY;
use function array_filter;
use function array_walk;
use function get_class;
use function gettype;
use function in_array;
use function is_object;

class ClassDiagramPageProcessor implements PageProcessor, AnnotationSubscriber
{
    /** @var ClassDependencies */
    private $dependencies;

    /** @var Packages */
    private $packages;

    /** @var string[] */
    private $creations = [];

    public function __construct(ClassDependencies $dependencies, Packages $packages)
    {
        $this->dependencies = $dependencies;
        $this->packages = $packages;
    }

    public function subscribe(AnnotationListeners $listeners): void
    {
        $listeners->notifyAboutMethodAnnotation(
            FactoryMethod::class,
            [$this, 'notifyAboutFactoryMethod']
        );
    }

    public function notifyAboutFactoryMethod(FactoryMethod $annotation, ReflectionMethod $method): void
    {
        if (! $method->getReturnType()
            || $method->getReturnType()->isBuiltin()
        ) {
            return;
        }

        $this->creations[$method->getDeclaringClass()->getName()][] = (string) $method->getReturnType();
    }

    public function process(Page $page): ProcessedPage
    {
        if (! $page instanceof ClassDiagramPage) {
            throw new InvalidArgumentException(get_class($page));
        }

        $definition = include $page->getDefinitionPath();
        if (! $definition instanceof ClassDiagramDescription) {
            throw new InvalidArgumentException(is_object($definition) ? get_class($definition) : gettype($definition));
        }

        $classes = $definition->getClasses();

        $diagram = new ClassDiagram();
        array_walk($classes, [$diagram, 'addClass']);
        foreach ($classes as $eachClass) {
            $associations = $this->filterDependencies(
                $this->dependencies->associationsOfClass($eachClass),
                $classes,
                ARRAY_FILTER_USE_KEY
            );
            foreach ($associations as $eachAssociation => $eachMultiplicity) {
                $diagram->addAssociation($eachClass, $eachAssociation, $eachMultiplicity);
            }
            $dependencies = $this->filterDependencies($this->dependencies->dependenciesOfClass($eachClass), $classes);
            foreach ($dependencies as $eachDependency) {
                $diagram->addDependency($eachClass, $eachDependency, '');
            }

            $dependencies = $this->filterDependencies($this->dependencies->extensionsOfClass($eachClass), $classes);
            foreach ($dependencies as $eachDependency) {
                $diagram->addGeneralization($eachClass, $eachDependency);
            }

            $dependencies = $this->filterDependencies(
                $this->dependencies->implementationsOfClass($eachClass),
                $classes
            );
            foreach ($dependencies as $eachDependency) {
                $diagram->addImplementation($eachClass, $eachDependency);
            }

            $dependencies = $this->filterDependencies(
                array_merge($this->dependencies->creationsOfClass($eachClass), $this->creations[$eachClass] ?? []),
                $classes
            );
            foreach ($dependencies as $eachDependency) {
                $diagram->addDependency($eachClass, $eachDependency, '«create»');
            }
        }
        $definition->addPredefinedToDiagram($diagram);

        $diagram->abbreviateClassNames();
        $diagram->removeRedundantArrows();

        $title = $definition->getTitle();
        return new ProcessedPage($definition->getTitle(), 'class_diagram', ['title' => $title, 'diagram' => $diagram]);
    }

    /**
     * @param string[] $dependencies
     * @param string[] $classes
     * @param int $flag
     * @return string[]
     */
    private function filterDependencies(array $dependencies, array $classes, int $flag = 0): array
    {
        return array_filter($dependencies, function (string $dependency) use ($classes) {
            return in_array($dependency, $classes);
        }, $flag);
    }
}
