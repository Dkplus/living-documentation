<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree\Exception;

use Dkplus\LivingDocumentation\SourceTree\Node;
use RuntimeException;

class NodeAddedTwice extends RuntimeException
{
    /** @var Node */
    private $node;

    /** @internal */
    public function __construct(Node $node)
    {
        parent::__construct(sprintf('Node "%s" has been added already', $node->name()));
        $this->node = $node;
    }

    public function relatedTo(): Node
    {
        return $this->node;
    }
}
