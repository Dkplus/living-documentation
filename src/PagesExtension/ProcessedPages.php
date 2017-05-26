<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\PagesExtension;

use IteratorAggregate;

interface ProcessedPages extends IteratorAggregate
{
    public function withId(string $id): ProcessedPage;

    /**
     * @param string[] $matchers
     * @return ProcessedPages
     */
    public function matching(array $matchers): ProcessedPages;

    /** @return string[] */
    public function ids(): array;
}
