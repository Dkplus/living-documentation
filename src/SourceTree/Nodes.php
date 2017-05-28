<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

use ArrayIterator;
use Dkplus\LivingDocumentation\SourceTree\Exception\NodeNotFound;
use IteratorIterator;
use function array_filter;
use function array_search;

class Nodes extends IteratorIterator
{
    /** @var Node[] */
    private $nodes;

    /** @internal */
    public function __construct(Node ...$nodes)
    {
        parent::__construct(new ArrayIterator($nodes));
        $this->nodes = $nodes;
    }

    public function current(): Node
    {
        return parent::current();
    }

    public function contains(Node $node): bool
    {
        return in_array($node, $this->nodes);
    }

    /** @throws NodeNotFound */
    public function without(Node $node): self
    {
        $index = array_search($node, $this->nodes, true);
        if ($index === false) {
            throw new NodeNotFound($node);
        }
        $nodes = $this->nodes;
        unset($nodes[$index]);
        return new self(...array_values($nodes));
    }

    public function filter(callable $filter): self
    {
        return new self(...array_filter($this->nodes, $filter));
    }
}
