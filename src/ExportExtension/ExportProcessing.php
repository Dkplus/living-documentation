<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\ExportExtension;

use Dkplus\LivingDocumentation\Extension\ProcessingStep;
use Dkplus\LivingDocumentation\PagesExtension\ProcessedPages;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function dirname;
use function file_put_contents;
use function is_dir;

class ExportProcessing implements ProcessingStep
{
    /** @var Export[][]|PageRenderer[][] */
    private $exports = [];

    /** @var ProcessedPages */
    private $pages;

    public function __construct(ProcessedPages $pages)
    {
        $this->pages = $pages;
    }

    public function addExport(Export $export, PageRenderer $renderer): void
    {
        $this->exports[] = [$export, $renderer];
    }


    public function process(InputInterface $input, OutputInterface $output): int
    {
        /* @var $export Export */
        /* @var $renderer PageRenderer */
        foreach ($this->exports as [$export, $renderer]) {
            $files = $export->render($this->pages, $renderer);
            foreach ($files as $eachPath => $eachContent) {
                $dir = dirname($eachPath);
                if (! is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                file_put_contents($eachPath, $eachContent);
            }
        }
        return 0;
    }
}
