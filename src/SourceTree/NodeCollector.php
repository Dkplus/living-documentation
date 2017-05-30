<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

interface NodeCollector
{
    public function collectNodes(Node $node, FamilyTree $nodes): void;
}
