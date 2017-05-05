<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

interface PageProcessor
{
    public function process(Page $page): ProcessedPage;
}
