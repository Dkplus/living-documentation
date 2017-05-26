<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\ExportExtension;

use function array_map;
use RuntimeException;

class MenuItem
{
    /** @var string */
    private $label;

    /** @var string */
    private $pageId;

    /** @var array */
    private $children;

    public function __construct(string $label, ?string $pageId, array $children)
    {
        $this->label = $label;
        $this->pageId = $pageId;
        $this->children = array_map(function (self $child) {
            return $child;
        }, $children);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function hasPageId(): bool
    {
        return $this->pageId !== null;
    }

    public function getPageId(): string
    {
        if ($this->pageId === null) {
            throw new RuntimeException("MenuItem with label {$this->label} has no page id");
        }
        return $this->pageId;
    }

    /** @return self[] */
    public function getChildren(): array
    {
        return $this->children;
    }
}
