<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeVisitorAbstract;

class TraitUsageVisitor extends NodeVisitorAbstract
{
    /** @var string[] */
    private $detectedTraits = [];

    public function beforeTraverse(array $nodes): void
    {
        $this->detectedTraits = [];
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof TraitUse) {
            return;
        }
        foreach ($node->traits as $trait) {
            $this->detectedTraits[] = $trait->toString();
        }
    }

    public function foundTraitUsages(): array
    {
        return $this->detectedTraits;
    }
}
