<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

final class ArrayType implements DecoratingType
{
    /** @var Type */
    private $type;

    public function __construct(Type $type = null)
    {
        $this->type = $type ?: new MixedType();
    }

    public function decoratedType(): Type
    {
        return $this->type;
    }

    public function allows(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'allows'], $type->decoratedTypes()));
        }
        if ($type instanceof self) {
            return $this->decoratedType()->allows($type->decoratedType());
        }
        return false;
    }

    public function __toString(): string
    {
        return $this->type instanceof MixedType
            ? 'array'
            : "array<{$this->type}>";
    }
}
