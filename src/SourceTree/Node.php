<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Exception\NodeNotFound;

abstract class Node
{
    /** @var FamilyTree */
    protected $familyTree;

    protected function __construct(FamilyTree $familyTree)
    {
        $this->familyTree = $familyTree;
    }

    abstract public function name(): string;

    public function findAncestorsOfClass(string $class): Nodes
    {
        return $this->familyTree->findAncestorsOf($this)->filter(function (Node $node) use ($class) {
            return $node instanceof $class;
        });
    }

    public function isDescendantOf(Node $anotherNode): bool
    {
        return $this->familyTree->findDescendantsOf($this)->contains($anotherNode);
    }

    public function findDescendantsOfClass(string $class): Nodes
    {
        return $this->familyTree->findAncestorsOf($this)->filter(function (Node $node) use ($class) {
            return $node instanceof $class;
        });
    }
}
