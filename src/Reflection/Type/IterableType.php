<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

use Traversable;

final class IterableType implements DecoratingType
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
        if ($this->type instanceof MixedType && $type instanceof ClassType) {
            return $type->reflection()->implementsInterface(Traversable::class);
        }
        if ($type instanceof self || $type instanceof ArrayType || $type instanceof CollectionType) {
            return $this->decoratedType()->allows($type->decoratedType());
        }
        return false;
    }

    public function __toString(): string
    {
        if ($this->type instanceof ComposedType) {
            return "({$this->type})[]";
        }
        return $this->type . '[]';
    }
}
