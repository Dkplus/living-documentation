<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection;

use ReflectionClass;
use function array_map;

class Interface_ extends ClassAlike
{
    /** @var Constant[] */
    private $constants;

    /** @var Interface_[] */
    private $interfaces;

    public function __construct(
        ReflectionClass $reflection,
        array $extendedInterfaces,
        array $constants,
        array $methods
    ) {
        parent::__construct($reflection, $methods);
        $this->interfaces = $extendedInterfaces;
        $this->constants = $constants;
    }

    public function explicitDefinedConstants(): array
    {
        return $this->constants;
    }

    public function definedConstants(): array
    {
        return array_unique(array_merge($this->constants, ...array_map(function (self $extended) {
            return $extended->explicitDefinedConstants();
        }, $this->extendedInterfaces())));
    }

    public function explicitExtendedInterfaces(): array
    {
        return $this->interfaces;
    }

    public function extendedInterfaces(): array
    {
        return array_unique(array_merge($this->interfaces, ...array_map(function (self $extended) {
            return $extended->extendedInterfaces();
        }, $this->interfaces)));
    }
}
