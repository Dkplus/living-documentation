<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

use Dkplus\LivingDocs\Extension\ProcessingStep;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PagesProcessing implements ProcessingStep
{
    /** @var string[][]|Page[][]|PageProcessor[][] */
    private $pages = [];

    /** @var ProcessedPageCollector */
    private $collector;

    public function __construct(ProcessedPageCollector $collector)
    {
        $this->collector = $collector;
    }

    public function addPage(string $id, Page $page, PageProcessor $processor): void
    {
        $this->pages[] = [$id, $page, $processor];
    }

    public function process(InputInterface $input, OutputInterface $output): int
    {
        /* @var string $id */
        /* @var $page Page */
        /* @var $converter PageProcessor */
        foreach ($this->pages as [$id, $page, $converter]) {
            $this->collector->add($id, $converter->process($page));
        }
        return 0;
    }
}
