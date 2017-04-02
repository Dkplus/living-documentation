<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering\Menu;

use Dkplus\LivingDocs\Rendering\Page\Page;
use function array_map;

class InactiveMenuItem implements MenuItem
{
    /** @var string */
    private $label;

    /** @var ?string */
    private $link;

    /** @var MenuItem[] */
    private $children;

    public static function page(string $label, string $link): self
    {
        return new self($label, $link, []);
    }

    public static function sectionWithContent(string $label, string $link, MenuItem ...$children): self
    {
        return new self($label, $link, $children);
    }

    public static function sectionWithoutContent(string $label, MenuItem ...$children): self
    {
        return new self($label, null, $children);
    }

    private function __construct(string $label, ?string $link, array $children)
    {
        $this->label = $label;
        $this->link = $link;
        $this->children = $children;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    /** @return self[] */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function isActive(): bool
    {
        return false;
    }

    public function activate(Page $page): MenuItem
    {
        if ($page->fileName() === $this->link) {
            return new ActiveMenuItem($this);
        }
        $children = array_map(function (MenuItem $child) use ($page) {
            return $child->activate($page);
        }, $this->children);
        if ($children !== $this->children) {
            return new self($this->label, $this->link, $children);
        }
        return $this;
    }
}
