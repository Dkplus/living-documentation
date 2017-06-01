<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

final class NullableType implements DecoratingType
{
    /** @var Type */
    private $decorated;

    public function __construct(Type $decorated)
    {
        $this->decorated = $decorated;
    }

    public function allows(Type $type): bool
    {
        if ($type instanceof ComposedType) {
            return ! in_array(false, array_map([$this, 'allows'], $type->decoratedTypes()));
        }
        if ($type instanceof NullType) {
            return true;
        }
        if ($type instanceof self) {
            return $this->decorated->allows($type->decorated);
        }
        return $this->decorated->allows($type);
    }

    public function __toString(): string
    {
        if ($this->decorated instanceof ComposedType) {
            return "?({$this->decorated})";
        }
        return '?' . $this->decorated;
    }

    public function decoratedType(): Type
    {
        return $this->decorated;
    }
}
