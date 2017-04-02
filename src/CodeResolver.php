<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs;

interface CodeResolver
{
    public function resolveClassCode(string $className): CodeSnippet;
    public function resolveFunction(callable $function): CodeSnippet;
}
