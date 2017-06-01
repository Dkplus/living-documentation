<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

final class StringType implements Type
{
    public function allows(Type $type): bool
    {
        return $type instanceof self;
    }

    public function __toString(): string
    {
        return 'string';
    }
}
