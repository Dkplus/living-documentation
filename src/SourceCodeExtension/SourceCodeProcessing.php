<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension;

use Dkplus\LivingDocumentation\Extension\ProcessingStep;
use Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing\ClassCollectingVisitor;
use Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing\CodeTraverser;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function array_map;
use function array_walk;
use function is_dir;

class SourceCodeProcessing implements ProcessingStep
{
    /** @var string[] */
    private $directories;

    /** @var CodeTraverser */
    private $traverser;

    /** @var CodeIterator */
    private $iterator;

    /** @var ClassCollectingVisitor */
    private $classes;

    public function __construct(
        array $directories,
        CodeTraverser $finder,
        CodeIterator $iterator,
        ClassCollectingVisitor $classes
    ) {
        $this->directories = array_map(function (string $directory) {
            if (! is_dir($directory)) {
                throw new RuntimeException("Is no directory: $directory");
            }
            return $directory;
        }, $directories);
        $this->traverser = $finder;
        $this->iterator = $iterator;
        $this->classes = $classes;
    }

    public function process(InputInterface $input, OutputInterface $output): int
    {
        array_walk($this->directories, [$this->traverser, 'traverse']);
        $this->iterator->iterateOver($this->classes->getClassNames());
        return 0;
    }
}
