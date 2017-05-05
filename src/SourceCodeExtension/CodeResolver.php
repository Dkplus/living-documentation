<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension;

interface CodeResolver
{
    public function resolveClassCode(string $className): CodeSnippet;
    public function resolveMethod(string $className, string $method): CodeSnippet;
    public function resolveFunction(string $function): CodeSnippet;
}
