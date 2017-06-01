<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Traversable;

class CollectionType extends ClassType
{
    /** @var ClassType */
    private $class;

    /** @var IterableType */
    private $generic;

    public function __construct(ClassType $class, Type $generic)
    {
        $this->class = $class;
        $this->generic = new IterableType($generic);
        if (! $class->reflection()->implementsInterface(Traversable::class)) {
            throw new InvalidArgumentException('Class ' . $class->reflection()->name() . ' is not traversable');
        }
        parent::__construct($class->reflection());
    }

    public function allows(Type $type): bool
    {
        return $type instanceof self
            && $this->class->allows($type->class)
            && $this->generic->allows($type->generic);
    }

    public function decoratedType(): Type
    {
        return $this->generic->decoratedType();
    }

    public function __toString(): string
    {
        return "{$this->class}|{$this->generic}";
    }
}
