<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

interface DecoratingType extends Type
{
    public function decoratedType(): Type;
}
