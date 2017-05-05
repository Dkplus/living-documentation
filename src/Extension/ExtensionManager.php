<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Extension;

use InvalidArgumentException;
use RuntimeException;
use function array_map;
use function array_merge;
use function class_exists;

class ExtensionManager
{
    /**
     * @var array|Extension[]
     */
    private $defaultExtensions;

    /** @var string[] */
    private $classNames = [];

    /**
     * @param array|Extension[] $defaultExtensions
     */
    public function __construct(array $defaultExtensions)
    {
        $this->defaultExtensions = array_map(function (Extension $extension) {
            return $extension;
        }, $defaultExtensions);
    }

    public function addExtensionsByClassName(array $classNames): void
    {
        $classNames = array_map(function (string $className) {
            if (! class_exists($className)) {
                throw new InvalidArgumentException($className . ' could not be autoloaded');
            }
        }, $classNames);
        $this->classNames = array_unique(array_merge($this->classNames, $classNames));
    }

    /**
     * @return Extension[]
     * @throws RuntimeException on invalid extension class found
     */
    public function instantiateExtensions(): array
    {
        $result = $this->defaultExtensions;
        foreach ($this->classNames as $each) {
            $extension = new $each;
            if (! $extension instanceof Extension) {
                throw new RuntimeException("$each is not an instance of " . Extension::class);
            }
            if ($extension->getConfigKey() === 'extensions') {
                throw new RuntimeException("$each uses 'extensions' as config key but this is a reserved key");
            }
            $result[] = $extension;
        }
        return $result;
    }
}
