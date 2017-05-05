<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension;

use Dkplus\LivingDocs\SourceCodeExtension\Listener\AnnotationSubscriber;
use Dkplus\LivingDocs\SourceCodeExtension\Listener\ClassListener;
use Dkplus\LivingDocs\SourceCodeExtension\Listener\MethodListener;
use Dkplus\LivingDocs\SourceCodeExtension\Listener\PropertyListener;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use function var_dump;
use Zend\Code\Reflection\DocBlockReflection;
use const ARRAY_FILTER_USE_KEY;
use function array_filter;
use function array_merge;
use function array_walk;

class AnnotationIterator implements AnnotationListeners, ClassListener, PropertyListener, MethodListener
{
    /** @var Reader */
    private $annotationReader;

    /** @var callable[][] */
    private $classListeners = [];

    /** @var callable[][] */
    private $propertyListeners = [];

    /** @var callable[][] */
    private $methodListeners = [];

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function registerSubscriber(AnnotationSubscriber $subscriber): void
    {
        $subscriber->subscribe($this);
    }

    public function notifyAboutClassAnnotation(string $annotationClassName, callable $listener): void
    {
        $this->classListeners[$annotationClassName][] = $listener;
    }

    public function notifyAboutPropertyAnnotation(string $annotationClassName, callable $listener): void
    {
        $this->propertyListeners[$annotationClassName][] = $listener;
    }

    public function notifyAboutMethodAnnotation(string $annotationClassName, callable $listener): void
    {
        $this->methodListeners[$annotationClassName][] = $listener;
    }

    public function notifyAboutClass(ReflectionClass $class): void
    {
        $annotations = $this->annotationReader->getClassAnnotations($class);

        if ($class->getDocComment()) {
            $annotations = array_merge(
                $annotations,
                (new DocBlockReflection((string) $class->getDocComment()))->getTags()
            );
        }

        $this->notifyListeners($this->classListeners, $annotations, $class);
    }

    /**
     * @param array|callable[] $listeners
     * @param array $annotations
     * @param mixed $reflection
     */
    private function notifyListeners(array $listeners, array $annotations, $reflection): void
    {
        foreach ($annotations as $eachAnnotation) {
            $eachListeners = array_filter($listeners, function (string $subscribedClass) use ($eachAnnotation) {
                return $eachAnnotation instanceof $subscribedClass;
            }, ARRAY_FILTER_USE_KEY);
            $eachListeners = array_merge([], ...array_values($eachListeners));
            array_walk($eachListeners, function (callable $listener) use ($reflection, $eachAnnotation, $annotations) {
                $listener($eachAnnotation, $reflection, $annotations);
            });
        }
    }

    public function notifyAboutMethod(ReflectionMethod $method): void
    {
        $annotations = $this->annotationReader->getMethodAnnotations($method);

        if ($method->getDocComment()) {
            $annotations = array_merge(
                $annotations,
                (new DocBlockReflection((string) $method->getDocComment()))->getTags()
            );
        }

        $this->notifyListeners($this->methodListeners, $annotations, $method);
    }

    public function notifyAboutProperty(ReflectionProperty $property): void
    {
        $annotations = $this->annotationReader->getPropertyAnnotations($property);

        if ($property->getDocComment()) {
            $annotations = array_merge(
                $annotations,
                (new DocBlockReflection((string) $property->getDocComment()))->getTags()
            );
        }

        $this->notifyListeners($this->propertyListeners, $annotations, $property);
    }
}
