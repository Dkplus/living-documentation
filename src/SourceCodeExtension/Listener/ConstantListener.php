<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension\Listener;

use ReflectionClass;

interface ConstantListener
{
    public function notifyAboutConstant(ReflectionClass $class, string $constantName, $constantValue): void;
}
