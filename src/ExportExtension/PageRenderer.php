<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\ExportExtension;

use Dkplus\LivingDocumentation\PagesExtension\ProcessedPage;

interface PageRenderer
{
    public function start(array $pages): void;
    public function render(string $id, ProcessedPage $page, array $extra): void;
    public function finish(): array;
}
