<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection;

use ReflectionClass;

abstract class ClassAlike
{
    /** @var ReflectionClass */
    protected $reflection;

    /** @var Method[] */
    private $methods;

    public function __construct(ReflectionClass $reflection, array $methods)
    {
        $this->reflection = $reflection;
        $this->methods = $methods;
    }

    public function name(): string
    {
        return $this->reflection->getName();
    }

    public function shortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function namespace(): string
    {
        return $this->reflection->getNamespaceName();
    }

    public function packageName(): string
    {
        $package = $this->reflection->getNamespaceName();
        if (preg_match('/^\s*\* @package (.*)/m', $this->reflection->getDocComment(), $matches)) {
            $package = $matches[1];
        }
        if (preg_match('/^\s*\* @subpackage (.*)/m', $this->reflection->getDocComment(), $matches)) {
            $package = $package . '\\' . $matches[1];
        }
        return $package;
    }

    public function constant(string $name): Constant
    {
    }

    public function constants(): array
    {
    }

    public function method(string $method): Method
    {
    }

    public function methods(): array
    {

    }
}
