<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

class ClassAlikeCollectingVisitor extends NodeVisitorAbstract
{
    /** @var string[] */
    private $classes = [];

    public function beforeTraverse(array $nodes)
    {
        $this->classes = [];
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_
            || $node instanceof Interface_
            || $node instanceof Trait_
        ) {
            $this->classes = (string) $node->name;
        }
    }

    /** @return string[] */
    public function classNames(): array
    {
        return $this->classes;
    }
}
