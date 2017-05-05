<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension\CodeTraversing;

use Dkplus\LivingDocs\SourceCodeExtension\Classes;
use PhpParser\Builder\Trait_;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeVisitorAbstract;

class ClassCollectingVisitor extends NodeVisitorAbstract implements Classes
{
    /** @var string[] */
    private $classes = [];

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_
            || $node instanceof Interface_
            || $node instanceof Trait_
        ) {
            $this->classes[] = (string) $node->namespacedName ?? 'anonymous@' . spl_object_hash($node);
        }
    }

    public function getClassNames(): array
    {
        return $this->classes;
    }
}
