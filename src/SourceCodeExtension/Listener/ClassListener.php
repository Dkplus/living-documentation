<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension\Listener;

use ReflectionClass;

interface ClassListener
{
    public function notifyAboutClass(ReflectionClass $class): void;
}
