<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\Listener;

use ReflectionMethod;

interface MethodListener
{
    public function notifyAboutMethod(ReflectionMethod $method): void;
}
