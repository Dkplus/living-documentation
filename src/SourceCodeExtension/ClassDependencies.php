<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension;

interface ClassDependencies
{
    /**
     * @param string $className
     * @return string[]
     */
    public function associationsOfClass(string $className): array;

    /**
     * @param string $className
     * @return string[]
     */
    public function dependenciesOfClass(string $className): array;

    /**
     * @param string $className
     * @return string[]
     */
    public function extensionsOfClass(string $className): array;

    /**
     * @param string $className
     * @return string[]
     */
    public function implementationsOfClass(string $className): array;

    /**
     * @param string $className
     * @return string[]
     */
    public function creationsOfClass(string $className): array;
}
