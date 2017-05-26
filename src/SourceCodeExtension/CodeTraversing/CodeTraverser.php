<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function array_walk;
use function file_get_contents;

class CodeTraverser
{
    /** @var NodeVisitor[] */
    private $visitors;

    public function __construct(NodeVisitor ...$visitors)
    {
        $this->visitors = $visitors;
    }

    public function traverse(string $directory): void
    {
        /* @var $files SplFileInfo[] */
        $files = Finder::create()->in($directory)->name('*.php')->files();
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        array_walk($this->visitors, [$traverser, 'addVisitor']);
        foreach ($files as $each) {
            $nodes = $parser->parse(file_get_contents($each->getRealPath()));
            $traverser->traverse($nodes);
        }
    }
}
