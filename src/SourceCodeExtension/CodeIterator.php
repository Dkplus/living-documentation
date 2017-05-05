<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension;

use Dkplus\LivingDocs\SourceCodeExtension\Listener\ClassListener;
use Dkplus\LivingDocs\SourceCodeExtension\Listener\ConstantListener;
use Dkplus\LivingDocs\SourceCodeExtension\Listener\MethodListener;
use Dkplus\LivingDocs\SourceCodeExtension\Listener\PropertyListener;
use ReflectionClass;
use function array_walk;

class CodeIterator
{
    /** @var ClassListener[] */
    private $classListeners = [];

    /** @var PropertyListener[] */
    private $propertyListeners = [];

    /** @var MethodListener[] */
    private $methodListeners = [];

    /** @var ConstantListener[] */
    private $constantListeners = [];

    public function addListener($listener): void
    {
        if ($listener instanceof ClassListener) {
            $this->classListeners[] = $listener;
        }
        if ($listener instanceof PropertyListener) {
            $this->propertyListeners[] = $listener;
        }
        if ($listener instanceof MethodListener) {
            $this->methodListeners[] = $listener;
        }
        if ($listener instanceof ConstantListener) {
            $this->constantListeners[] = $listener;
        }
    }

    public function iterateOver(array $classes): void
    {
        foreach ($classes as $eachClassName) {
            $eachClass = new ReflectionClass($eachClassName);
            $this->iterateOverClass($eachClass);
        }
    }

    private function iterateOverClass(ReflectionClass $class): void
    {
        array_walk($this->classListeners, function (ClassListener $listener) use ($class) {
            $listener->notifyAboutClass($class);
        });
        array_walk($this->constantListeners, function (ConstantListener $listener) use ($class) {
            foreach ($class->getConstants() as $eachName => $eachValue) {
                $listener->notifyAboutConstant($class, $eachName, $eachValue);
            }
        });
        array_walk($this->propertyListeners, function (PropertyListener $listener) use ($class) {
            foreach ($class->getProperties() as $eachProperty) {
                $listener->notifyAboutProperty($eachProperty);
            }
        });
        array_walk($this->methodListeners, function (MethodListener $listener) use ($class) {
            foreach ($class->getMethods() as $eachMethod) {
                $listener->notifyAboutMethod($eachMethod);
            }
        });
    }
}
