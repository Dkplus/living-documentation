<?php
declare(strict_types=1);

namespace test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures;

use Serializable;
use Traversable;

class ClassImplementingTwoInterfaces implements Traversable, Serializable
{
    public function serialize()
    {
    }

    public function unserialize($serialized)
    {
    }
}
