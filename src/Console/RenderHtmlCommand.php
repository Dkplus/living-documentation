<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Console;

use BetterReflection\Reflector\ClassReflector;
use BetterReflection\Reflector\FunctionReflector;
use BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use BetterReflection\SourceLocator\Type\EvaledCodeSourceLocator;
use BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Dkplus\LivingDocs\BetterReflection\BetterReflectionCodeResolver;
use Dkplus\LivingDocs\Documentation;
use Dkplus\LivingDocs\Rendering\MultipleConverters;
use Dkplus\LivingDocs\Rendering\Page\Page;
use Dkplus\LivingDocs\Rendering\PageCollector;
use Dkplus\LivingDocs\Rendering\PageRenderer;
use Dkplus\LivingDocs\Section\SectionConverter;
use Dkplus\LivingDocs\Sightseeing\SightseeingConverter;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
use function var_dump;

class RenderHtmlCommand extends Command
{
    public function __construct()
    {
        parent::__construct('render:html');
        $this->setDefinition([
            new InputArgument('target', InputArgument::REQUIRED),
            new InputArgument('source', InputArgument::IS_ARRAY),
            new InputOption('source-dir', 'd', InputArgument::OPTIONAL),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
            if (! $content instanceof Documentation) {
                $output->writeln("<error>$path must return one or an array of " . Documentation::class . '</error>');
                return false;
            }
            return $content;
        }, $sources);
        if (count(array_filter($sources, 'is_bool')) > 0) {
            return 1;
        }

        $codeResolver = new BetterReflectionCodeResolver(
            ClassReflector::buildDefaultReflector(),
            new FunctionReflector(new AggregateSourceLocator([
                new PhpInternalSourceLocator(),
                new EvaledCodeSourceLocator(),
                new AutoloadSourceLocator(),
            ])),
            new Standard()
        );

        $pages = new PageCollector();
        $converters = new MultipleConverters(
            new SectionConverter(),
            new SightseeingConverter($codeResolver)
        );

        foreach ($sources as $each) {
            $converters->convert($each, $pages, $converters);
        }

        $twig = new Twig_Environment(new Twig_Loader_Filesystem([__DIR__ . '/../../resources/templates']));
        $twig->addFunction(new Twig_Function('dump', function ($var) {
            var_dump($var);
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
