<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\Listener;

use ReflectionClass;

interface ClassListener
{
    public function notifyAboutClass(ReflectionClass $class): void;
}
