<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Exception\NodeAddedTwice;
use Dkplus\LivingDocumentation\SourceTree\Exception\NodeNotFound;
use SplObjectStorage;
use function array_map;
use function array_merge;
use function iterator_to_array;

class FamilyTree
{
    /** @var Project */
    private $progenitor;

    /** @var SplObjectStorage|Spouses[] */
    private $nodeToSpouses;

    public function __construct()
    {
        $this->progenitor = new Project($this);
        $this->nodeToSpouses = new SplObjectStorage();
        $this->nodeToSpouses[$this->progenitor] = Spouses::progenitor($this->progenitor);
    }

    public function progenitor(): Node
    {
        return $this->progenitor;
    }

    public function adopt(Node $ancestor, Node $descendant): void
    {
        $this->assertIsFamilyMember($ancestor);
        $this->assertIsNoFamilyMember($descendant);

        $this->nodeToSpouses[$descendant] = $this->nodeToSpouses[$ancestor]->adopt($descendant);
    }

    public function marry(Node $node, Node $partner): void
    {
        $this->assertIsFamilyMember($node);
        $this->assertIsNoFamilyMember($partner);

        $this->nodeToSpouses[$node]->marry($partner);
        $this->nodeToSpouses[$partner] = $this->nodeToSpouses[$node];
    }

    private function assertIsFamilyMember(Node $node): void
    {
        if (! isset($this->nodeToSpouses[$node])) {
            throw new NodeNotFound($node);
        }
    }

    private function assertIsNoFamilyMember(Node $node): void
    {
        if (isset($this->nodeToSpouses[$node])) {
            throw new NodeAddedTwice($node);
        }
    }

    public function findDescendantsOf(Node $node): Nodes
    {
        $this->assertIsFamilyMember($node);
        $descendants = $this->nodeToSpouses[$node]->descendants();
        $descendantNodes = array_map(function (Spouses $spouses) {
            return iterator_to_array($spouses->all());
        }, $descendants);
        return new Nodes(...array_merge([], ...$descendantNodes));
    }

    public function findAncestorsOf(Node $node): Nodes
    {
        $this->assertIsFamilyMember($node);
        $ancestors = $this->nodeToSpouses[$node]->ancestors();
        $ancestorNodes = array_map(function (Spouses $spouses) {
            return iterator_to_array($spouses->all());
        }, $ancestors);
        return new Nodes(...array_merge([], ...$ancestorNodes));
    }

    public function findSpousesOf(Node $node): Nodes
    {
        $this->assertIsFamilyMember($node);
        return $this->nodeToSpouses[$node]->all()->without($node);
    }
}
