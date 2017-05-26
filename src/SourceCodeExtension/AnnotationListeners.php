<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension;

interface AnnotationListeners
{
    public function notifyAboutClassAnnotation(string $annotationClassName, callable $listener): void;
    public function notifyAboutPropertyAnnotation(string $annotationClassName, callable $listener): void;
    public function notifyAboutMethodAnnotation(string $annotationClassName, callable $listener): void;
}
