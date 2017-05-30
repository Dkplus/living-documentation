<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Assert\Assert;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\NodeCollector;
use Dkplus\LivingDocumentation\SourceTree\Project;
use function array_map;
use function file_exists;
use function glob;
use function is_dir;

class FileCollector implements NodeCollector
{
    /** @var string[] */
    private $paths;

    public function __construct(array $paths)
    {
        $this->paths = array_map(function (string $path) {
            Assert::that(file_exists($path))->true("'$path' is no valid path");
            return $path;
        }, $paths);
    }

    public function collectNodes(Node $node, FamilyTree $nodes): void
    {
        if (! $node instanceof Project) {
            return;
        }
        foreach ($this->paths as $each) {
            $this->addPath($each, $node, $nodes);
        }
    }

    private function addPath(string $path, Node $parent, FamilyTree $nodes): void
    {
        if (is_dir($path)) {
            $this->addDirectoryRecursive($path, $parent, $nodes);
            return;
        }
        $this->addFile($path, $parent, $nodes);
    }

    private function addDirectoryRecursive(string $directory, Node $parentNode, FamilyTree $nodes): void
    {
        $node = new Directory($directory, $nodes);
        $nodes->adopt($parentNode, $node);
        foreach (glob($directory . '/*') as $each) {
            $this->addPath($each, $node, $nodes);
        }
    }

    private function addFile(string $filePath, Node $parentNode, FamilyTree $nodes): void
    {
        $nodes->adopt($parentNode, new File($filePath, $nodes));
    }
}
