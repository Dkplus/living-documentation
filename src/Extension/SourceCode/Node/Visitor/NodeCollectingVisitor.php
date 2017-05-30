<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\ClassAlike;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\File;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Package;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

class NodeCollectingVisitor extends NodeVisitorAbstract
{
    /** @var string */
    private $namespace = '';

    /** @var ClassAlike[][] */
    private $packageToClassMap = [];

    /** @var File */
    private $currentFile;

    /** @var FamilyTree */
    private $nodes;

    public function __construct(FamilyTree $familyTree)
    {
        $this->nodes = $familyTree;
    }

    public function beforeFile(File $file): void
    {
        $this->currentFile = $file;
        $this->namespace = '';
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            $this->namespace = (string) $node->name;
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_
            || $node instanceof Interface_
            || $node instanceof Trait_
        ) {
            $package = $this->namespace;
            $docNode = $node->getDocComment();
            $docText = $docNode ? $docNode->getText() : '';
            if (preg_match('/^\s*\* @package (.*)/m', $docText, $matches)) {
                $package = $matches[1];
            }
            if (preg_match('/^\s*\* @subpackage (.*)/m', $docText, $matches)) {
                $package = $package . '\\' . $matches[1];
            }

            $className = (string) $node->namespacedName ?? 'anonymous@' . spl_object_hash($node);
            $class = new ClassAlike($className);
            $this->packageToClassMap[$package][] = $class;
            $this->nodes->adopt($this->currentFile, $class);
        }
    }

    public function afterFile(): void
    {
        $this->registerNodes($this->nodes);
    }

    public function registerNodes(FamilyTree $nodes): void
    {
        foreach ($this->packageToClassMap as $eachPackage => $classes) {
            $package = new Package($eachPackage);
            $nodes->adopt($nodes->progenitor(), $package);
            foreach ($classes as $eachClass) {
                $nodes->adopt($package, $eachClass);
            }
        }
    }
}
