<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension\CodeTraversing;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use function get_class;
use ReflectionClass;
use function var_dump;

class MethodDependencyCollector extends NodeVisitorAbstract
{
    /** @var string[] */
    private $dependencies = [];

    /** @var string[] */
    private $instantiations = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof New_) {
            $this->instantiations[] = $this->getNameOfNode($node);
        }
        if ($node instanceof StaticCall
            && ! in_array($this->getNameOfNode($node->class), ['parent', 'self', 'static'])
        ) {
            $class = new ReflectionClass($this->getNameOfNode($node->class));
            $method = $class->getMethod((string) $node->name);
            if ($method->getReturnType()
                && ! $method->getReturnType()->isBuiltin()
                && in_array((string) $method->getReturnType(), [$class->getName(), 'static', 'self'], true)
            ) {
                $this->instantiations[] = $this->getNameOfNode($node);
            } else {
                $this->dependencies[] = $this->getNameOfNode($node);
            }
        }
        if ($node instanceof Instanceof_ || $node instanceof ClassConstFetch) {
            $this->dependencies[] = $this->getNameOfNode($node->class);
        }
    }

    private function getNameOfNode($node): string
    {
        if (is_string($node)) {
            return $node;
        }
        if ($node instanceof FullyQualified) {
            return (string) $node;
        }
        if ($node instanceof New_) {
            return $this->getNameOfNode($node->class);
        }
        if (isset($node->class)) {
            return $this->getNameOfNode($node->class);
        }
        if ($node instanceof Name) {
            return (string) implode($node->parts);
        }
        if (isset($node->name) && $node->name instanceof Variable) {
            return $this->getNameOfNode($node->name);
        }
        if (isset($node->name) && $node->name instanceof MethodCall) {
            return $this->getNameOfNode($node->name);
        }
        if ($node instanceof ArrayDimFetch) {
            return $this->getNameOfNode($node->var);
        }
        if (isset($node->name) && $node->name instanceof BinaryOp) {
            return get_class($node->name);
        }
        if ($node instanceof PropertyFetch) {
            return $this->getNameOfNode($node->var);
        }
        if (isset($node->name) && ! is_string($node->name)) {
            return $this->getNameOfNode($node->name);
        }
        if (isset($node->name) && null === $node->name) {
            return 'anonymous@' . spl_object_hash($node);
        }
        if (isset($node->name)) {
            return (string) $node->name;
        }
        if ($node instanceof Class_) {
            return 'anonymous@' . spl_object_hash($node);
        }
        return (string) $node;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getInstantiations(): array
    {
        return $this->instantiations;
    }
}
