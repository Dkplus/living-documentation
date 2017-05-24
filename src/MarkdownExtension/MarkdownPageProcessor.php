<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\MarkdownExtension;

use Dkplus\LivingDocs\PagesExtension\Page;
use Dkplus\LivingDocs\PagesExtension\PageProcessor;
use Dkplus\LivingDocs\PagesExtension\ProcessedPage;
use InvalidArgumentException;
use function get_class;

class MarkdownPageProcessor implements PageProcessor
{
    public function preProcess(Page $page): void
    {
    }

    public function process(Page $page): ProcessedPage
    {
        if (! $page instanceof MarkdownPage) {
            throw new InvalidArgumentException(get_class($page));
        }
        return new ProcessedPage($page->getTitle(), 'markdown', ['markdown' => $page->getMarkdownText()]);
    }
}
