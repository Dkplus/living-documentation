<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

class ProcessedPageCollector extends ArrayProcessedPages
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function add(string $id, ProcessedPage $page): void
    {
        $this->pages[$id] = $page;
    }
}
