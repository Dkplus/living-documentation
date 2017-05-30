<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing\ClassCollectingVisitor;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\NodeCollector;
use Dkplus\LivingDocumentation\SourceTree\Project;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use function file_get_contents;

class PhpCollector implements NodeCollector
{
    public function collectNodes(Node $node, FamilyTree $nodes): void
    {
        if (! $node instanceof Project) {
            return;
        }
        /* @var $phpFiles File[] */
        $phpFiles = $nodes->findDescendantsOf($node)->filter(function (Node $node) {
            return $node instanceof File && $node->extension() === 'php';
        });
        foreach ($phpFiles as $eachFile) {
            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
            $traverser = new NodeTraverser();
            $traverser->addVisitor(new NameResolver());
            $traverser->addVisitor(new ClassCollectingVisitor());
            $traverser->traverse($parser->parse(file_get_contents($eachFile->path())));
        }
    }
}
