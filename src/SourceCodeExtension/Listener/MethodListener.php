<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension\Listener;

use ReflectionMethod;

interface MethodListener
{
    public function notifyAboutMethod(ReflectionMethod $method): void;
}
