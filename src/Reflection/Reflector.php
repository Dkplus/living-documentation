<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection;

interface Reflector
{
    public function reflectFile(string $fileName): array;
    public function reflectClass(string $fcqn): ClassAlike;
    public function reflectMethod(string $fcqn, string $method): Method;
    public function reflectProperty(string $fcqn, string $property): Property;
}
