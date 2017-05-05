<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\ExportExtension;

use Dkplus\LivingDocs\PagesExtension\ProcessedPage;

interface PageRenderer
{
    public function start(array $pageIds): void;
    public function render(string $id, ProcessedPage $page, array $extra): void;
    public function finish(): array;
}
