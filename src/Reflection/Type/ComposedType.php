<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

class ComposedType implements Type
{
    /** @var Type[] */
    private $types;

    public function __construct(Type $firstType, Type $secondType, Type ...$moreTypes)
    {
        $this->types = array_merge([$firstType, $secondType], $moreTypes);
    }

    public function allows(Type $type): bool
    {
        return in_array(true, array_map(function (Type $decorated) use ($type) {
            return $decorated->allows($type);
        }, $this->decoratedTypes()));
    }

    public function __toString(): string
    {
        return implode('|', array_map('strval', $this->decoratedTypes()));
    }

    /** @return DecoratingType[] */
    public function decoratedTypes(): array
    {
        return $this->types;
    }
}
