<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\PagesExtension;

interface PageProcessor
{
    public function preProcess(Page $page): void;
    public function process(Page $page): ProcessedPage;
}
