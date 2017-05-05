<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension\Listener;

use Dkplus\LivingDocs\SourceCodeExtension\AnnotationListeners;

interface AnnotationSubscriber
{
    public function subscribe(AnnotationListeners $listeners): void;
}
