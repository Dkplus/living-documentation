<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

interface NodeHierarchy
{
    public function findDescendantsOf(Node $node): Nodes;

    public function findAncestorsOf(Node $node): Nodes;

    public function findSpousesOf(Node $node): Nodes;
}
