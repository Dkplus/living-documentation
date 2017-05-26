<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing;

use Dkplus\LivingDocumentation\SourceCodeExtension\ClassDependencies;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Zend\Code\Reflection\DocBlock\Tag\GenericTag;
use Zend\Code\Reflection\DocBlockReflection;
use function array_diff;
use function array_filter;
use function array_merge;
use function array_unique;
use function array_values;
use function is_array;
use function mb_substr;
use function var_dump;

class ClassDependencyCollectingVisitor extends NodeVisitorAbstract implements ClassDependencies
{
    /** @var string[][] */
    private $classExtensions = [];

    /** @var string[][] */
    private $classImplementations = [];

    /** @var string[][] */
    private $classDependencies = [];

    /** @var string[][] class as key and multiplicity as value */
    private $classAssociations = [];

    /** @var string */
    private $namespace;

    /** @var string[] Alias to FCQN */
    private $uses = [];

    /** @var string[][] */
    private $classCreations = [];

    public function leaveNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            $this->namespace = (string) $node->name;
            $this->uses = [];
        }

        if ($node instanceof Use_) {
            foreach ($node->uses as $use) {
                $this->uses[$use->alias] = (string) $use->name;
            }
        }

        if (! $node instanceof Class_ && ! $node instanceof Interface_ && ! $node instanceof Trait_) {
            return;
        }

        $className = (string) $node->namespacedName ?? 'anonymous@' . spl_object_hash($node);
        $this->classDependencies[$className] = [];

        if (isset($node->extends)) {
            if (is_array($node->extends)) {
                foreach ((array) $node->extends as $interface) {
                    $this->classExtensions[$className][] = (string) $interface;
                }
            } else {
                $this->classExtensions[$className][] = (string) $node->extends;
            }
        }

        if (isset($node->implements)) {
            foreach ((array) $node->implements as $interface) {
                $this->classImplementations[$className][] = (string) $interface;
            }
        }
        $methodDependencies = [];
        $methodInstantiations = [];
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Property) {
                $this->classAssociations[$className] = array_merge(
                    $this->classAssociations[$className] ?? [],
                    $this->getDependenciesFromDocBlock($stmt->getDocComment())
                );
            }

            if (! $stmt instanceof ClassMethod) {
                continue;
            }

            // doc block
            $methodDependencies[] = array_values($this->getDependenciesFromDocBlock($stmt->getDocComment()));

            // return
            if (isset($stmt->returnType) && $stmt->returnType instanceof FullyQualified) {
                $this->classDependencies[$className][] = (string) $stmt->returnType;
            }
            // Type hint of method's parameters
            foreach ($stmt->params as $param) {
                if ($param->type && $param->type instanceof FullyQualified) {
                    $this->classDependencies[$className][] = (string) $param->type;
                }
            }
            // instantiations, static calls
            $myVisitor = new MethodDependencyCollector();
            $traverser = new NodeTraverser();
            $traverser->addVisitor($myVisitor);
            $traverser->traverse([$node]);
            $methodDependencies[] = $myVisitor->getDependencies();
            $methodInstantiations[] = $myVisitor->getInstantiations();
        }
        $this->classDependencies[$className] = array_diff(array_unique(array_merge(
            $this->classDependencies[$className] ?? [],
            ...$methodDependencies
        )), [$className]);
        $this->classCreations[$className] = array_diff(array_unique(array_merge(
            $this->classCreations[$className] ?? [],
            ...$methodInstantiations
        )), [$className]);
    }

    private function getDependenciesFromDocBlock(?Doc $docBlock): array
    {
        if (! $docBlock) {
            return [];
        }
        $tags = (new DocBlockReflection($docBlock->getReformattedText()))->getTags();

        /* @var $tags GenericTag[] */
        $tags = array_filter($tags, function ($tag) {
            return $tag instanceof GenericTag
                && in_array($tag->getName(), ['var', 'return', 'param', 'throws']);
        });
        $ignoredValues = [
            'string',
            'int',
            'integer',
            'float',
            'double',
            'bool',
            'boolean',
            'object',
            'mixed',
            'resource',
            'callable',
            'callback',
        ];
        $result = [];
        foreach ($tags as $each) {
            $tagValues = explode('|', $each->getContent());
            $tagValues = array_diff($tagValues, $ignoredValues);
            foreach ($tagValues as $eachClassName) {
                if (($firstWhitespace = mb_strpos(' ', $eachClassName)) !== false) {
                    $eachClassName = mb_substr($eachClassName, 0, $firstWhitespace);
                }
                $multiplicity = '1';
                if (mb_substr($eachClassName, -2) === '[]') {
                    $multiplicity = '*';
                    $eachClassName = trim($eachClassName, '[]');
                }
                if (mb_strpos($eachClassName, '\\') === 0) {
                    $result[$eachClassName] = $multiplicity;
                    continue;
                }
                $result[$this->uses[$eachClassName] ?? $this->namespace . '\\' . $eachClassName] = $multiplicity;
            }
        }
        return $result;
    }

    public function extensionsOfClass(string $className): array
    {
        return $this->classExtensions[$className] ?? [];
    }

    public function implementationsOfClass(string $className): array
    {
        return $this->classImplementations[$className] ?? [];
    }

    public function associationsOfClass(string $className): array
    {
        return $this->classAssociations[$className] ?? [];
    }

    public function dependenciesOfClass(string $className): array
    {
        return $this->classDependencies[$className] ?? [];
    }

    public function creationsOfClass(string $className): array
    {
        return $this->classCreations[$className] ?? [];
    }
}
