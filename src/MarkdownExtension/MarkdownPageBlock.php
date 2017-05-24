<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\MarkdownExtension;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Cursor;

class MarkdownPageBlock extends AbstractBlock
{
    /** @var string */
    private $pageId;

    public function __construct(string $pageId)
    {
        parent::__construct();
        $this->pageId = $pageId;
    }

    public function getPageId(): string
    {
        return $this->pageId;
    }

    public function canContain(AbstractBlock $block): bool
    {
        return false;
    }

    public function acceptsLines(): bool
    {
        return false;
    }

    public function isCode(): bool
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor): bool
    {
        return false;
    }
}
