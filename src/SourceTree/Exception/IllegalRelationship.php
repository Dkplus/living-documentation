<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree\Exception;

use Dkplus\LivingDocumentation\SourceTree\Node;
use RuntimeException;

class IllegalRelationship extends RuntimeException
{
    /** @var Node */
    private $firstNode;

    /** @var Node */
    private $secondNode;

    /** @internal */
    public function __construct(Node $node, Node $secondNode)
    {
        parent::__construct('Node cannot be an ancestor/descendant of itself');
        $this->firstNode = $node;
        $this->secondNode = $secondNode;
    }

    public function firstInvolved(): Node
    {
        return $this->firstNode;
    }

    public function secondInvolved(): Node
    {
        return $this->secondNode;
    }
}
