<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

use Dkplus\LivingDocs\Extension\ProcessingStep;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PagesPreProcessing implements ProcessingStep
{
    /** @var string[][]|Page[][]|PageProcessor[][] */
    private $pages = [];

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
            $converter->preProcess($page);
        }
        return 0;
    }
}
