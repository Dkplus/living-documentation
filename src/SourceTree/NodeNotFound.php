<?php

namespace Dkplus\LivingDocumentation\SourceTree;

use RuntimeException;

class NodeNotFound extends RuntimeException
{
    public static function asAncestorOfClass(string $class, Node $origin): self
    {
        return new self(sprintf(
            'There is no ancestor of %s that is of class %s',
            $origin->name(),
            $class
        ));
    }
}
