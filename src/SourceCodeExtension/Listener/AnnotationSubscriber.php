<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\Listener;

use Dkplus\LivingDocumentation\SourceCodeExtension\AnnotationListeners;

interface AnnotationSubscriber
{
    public function subscribe(AnnotationListeners $listeners): void;
}
