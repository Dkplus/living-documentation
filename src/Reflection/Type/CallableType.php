<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

final class CallableType implements Type
{
    public function allows(Type $type): bool
    {
        if ($type instanceof self) {
            return true;
        }
        if ($type instanceof ClassType) {
            return $type->reflection()->isInvokable();
        }
        return false;
    }

    public function __toString(): string
    {
        return 'callable';
    }
}
