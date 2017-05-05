<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Extension;

use Dkplus\LivingDocs\GlossaryExtension\GlossaryPageProcessor;
use Dkplus\LivingDocs\Rendering\MultipleConverters;
use Dkplus\LivingDocs\Rendering\Page\Page;
use Dkplus\LivingDocs\Rendering\PageCollector;
use Dkplus\LivingDocs\Rendering\PageRenderer;
use Dkplus\LivingDocs\Section\SectionPageProcessor;
use Dkplus\LivingDocs\Sightseeing\SightseeingPageProcessor;
use Dkplus\LivingDocs\SourceCodeExtension\AnnotationIterator;
use Dkplus\LivingDocs\SourceCodeExtension\CodeIterator;
use Dkplus\LivingDocs\SourceCodeExtension\CodeTraversing\CodeTraverser;
use Dkplus\LivingDocs\SourceCodeExtension\CodeTraversing\PackageCollectingVisitor;
use Dkplus\LivingDocs\SourceCodeExtension\SimpleCodeResolver;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig_Environment;
use Twig_Function;
use Twig_Loader_Filesystem;
use function array_filter;
use function array_map;
use function dirname;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function mkdir;

class Command extends BaseCommand
{
    /** @var Processor */
    private $processor;

    public function __construct(string $name, Processor $processor)
    {
        parent::__construct($name);
        $this->processor = $processor;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->processor->process($input, $output);
        $sources = $input->getArgument('source');
        $target = $input->getArgument('target');

        $sourceDir = './';
        if ($input->hasOption('source-dir')) {
            $sourceDir = rtrim($input->getOption('source-dir'), '/') . '/';
        }

        $sources = array_map(function (string $source) use ($sourceDir) {
            if (strpos($source, '/') !== 0) {
                return $sourceDir . $source;
            }
            return $source;
        }, $sources);

        $notExistingSources = array_filter($sources, function ($path) {
            return ! file_exists($path);
        });
        foreach ($notExistingSources as $each) {
            $output->writeln("<error>File $each does not exist</error>");
        }
        if (count($notExistingSources) > 1) {
            return 1;
        }
        $sources = array_map(function (string $path) use ($output) {
            $content = include $path;
            if (! $content instanceof Page) {
                $output->writeln("<error>$path must return one or an array of " . Page::class . '</error>');
                return false;
            }
            return $content;
        }, $sources);
        if (count(array_filter($sources, 'is_bool')) > 0) {
            return 1;
        }

        $packages = new PackageCollectingVisitor();

        $reflectionIterator = new CodeIterator(
            (new CodeTraverser())->findInDirectory(__DIR__ . '/../../src', $packages), //@todo: remove
            []
        );
        $codeResolver = new SimpleCodeResolver();
        $annotationIterator = new AnnotationIterator($reflectionIterator, new AnnotationReader());

        $glossaryConverter = new GlossaryPageProcessor($packages);
        $glossaryConverter->registerAnnotationListeners($annotationIterator);

        $annotationIterator->run();

        $pages = new PageCollector();
        $converters = new MultipleConverters(
            new SectionPageProcessor(),
            $glossaryConverter,
            new SightseeingPageProcessor($codeResolver)
        );

        foreach ($sources as $each) {
            $converters->process($each, $pages, $converters);
        }

        $twig = new Twig_Environment(new Twig_Loader_Filesystem([__DIR__ . '/../../resources/templates']));
        $twig->addFunction(new Twig_Function('dump', function ($var) {
            \var_dump($var);
        }));
        $renderer = new PageRenderer($twig);

        $pages->each(function (Page $page) use ($target, $renderer, $pages) {
            $dir = dirname($target . '/' . $page->fileName());
            if (! is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($target . '/' . $page->fileName(), $renderer->render($page, $pages));
        });
    }
}
