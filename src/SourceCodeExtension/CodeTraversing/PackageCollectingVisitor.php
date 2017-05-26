<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing;

use Dkplus\LivingDocumentation\SourceCodeExtension\Packages;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;
use function array_unique;

class PackageCollectingVisitor extends NodeVisitorAbstract implements Packages
{
    /** @var string */
    private $namespace;

    /** @var string[] */
    private $classToPackageMap = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            $this->namespace = (string) $node->name;
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_ || $node instanceof Interface_ || $node instanceof Trait_) {
            $package = $this->namespace;

            $docComment = $node->getDocComment();
            $docBlockText = $docComment ? $docComment->getText() : '';
            if (preg_match('/^\s*\* @package (.*)/m', $docBlockText, $matches)) {
                $package = $matches[1];
            }
            if (preg_match('/^\s*\* @subpackage (.*)/m', $docBlockText, $matches)) {
                $package = $package . '\\' . $matches[1];
            }

            $className = (string) $node->namespacedName ?? 'anonymous@' . spl_object_hash($node);
            $this->classToPackageMap[$className] = $package;
        }
    }

    public function getPackageOfClass(string $className): string
    {
        return $this->classToPackageMap[$className] ?? '';
    }

    public function getPackages(): array
    {
        return array_unique($this->classToPackageMap);
    }
}
