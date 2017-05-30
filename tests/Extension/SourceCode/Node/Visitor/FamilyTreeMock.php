<?php
declare(strict_types=1);

namespace test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor;

use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use Dkplus\LivingDocumentation\SourceTree\Node;
use function array_filter;

class FamilyTreeMock extends FamilyTree
{
    /** @var Node[][] */
    private $adoptions = [];

    /** @param callable|Node|null $adoptiveFilter */
    public function hasAdopted(callable $adoptedFilter, $adoptiveFilter = null): bool
    {
        if ($adoptiveFilter instanceof Node) {
            $adoptive = $adoptiveFilter;
            $adoptiveFilter = function ($node) use ($adoptive) {
                return $node == $adoptive;
            };
        }
        $filter = function (array $adoption) use ($adoptedFilter, $adoptiveFilter) {
            return $adoptedFilter($adoption[1])
                && (! $adoptiveFilter || $adoptiveFilter($adoption[0]));
        };
        return count(array_filter($this->adoptions, $filter)) > 0;
    }

    public function adopt(Node $ancestor, Node $descendant): void
    {
        $this->adoptions[] = [$ancestor, $descendant];
    }
}
