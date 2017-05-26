<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\Listener;

use ReflectionProperty;

interface PropertyListener
{
    public function notifyAboutProperty(ReflectionProperty $property): void;
}
