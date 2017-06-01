<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

final class BooleanType implements Type
{
    public function allows(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'allows'], $type->decoratedTypes()));
        }
        return $type instanceof self;
    }

    public function __toString(): string
    {
        return 'bool';
    }
}
