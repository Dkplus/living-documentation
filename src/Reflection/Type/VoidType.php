<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

final class VoidType implements Type
{
    public function __toString(): string
    {
        return 'void';
    }

    public function allows(Type $type): bool
    {
        return false;
    }
}
