<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

final class NullType implements Type
{
    public function __toString(): string
    {
        return 'null';
    }

    public function allows(Type $type): bool
    {
        return $type instanceof self;
    }
}
