<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension;

interface Packages
{
    public function getPackageOfClass(string $className): string;

    /** @return string[] */
    public function getPackages(): array;
}
