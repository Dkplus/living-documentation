<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

interface Type
{
    public function allows(Type $type): bool;
    public function __toString(): string;
}
