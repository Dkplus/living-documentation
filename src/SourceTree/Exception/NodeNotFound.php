<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree\Exception;

use Dkplus\LivingDocumentation\SourceTree\Node;
use RuntimeException;

final class NodeNotFound extends RuntimeException
{
    /** @var Node */
    private $node;

    /** @internal */
    public function __construct(Node $node)
    {
        parent::__construct('Node has not been added before');
        $this->node = $node;
    }

    public function relatedTo(): Node
    {
        return $this->node;
    }
}
