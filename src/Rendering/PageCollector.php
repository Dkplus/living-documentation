<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use function array_walk;
use Dkplus\LivingDocs\Rendering\Menu\MenuItem;
use Dkplus\LivingDocs\Rendering\Page\Page;
use Dkplus\LivingDocs\Rendering\Page\Pages;

class PageCollector implements Configuration, Pages
{
    /** @var SinglePage[] */
    private $pages = [];

    /** @var MenuItem[] */
    private $menuItems = [];

    /** @var LinkResolver */
    private $links;

    public function __construct()
    {
        $this->links = new LinkResolver();
    }

    public function addPage(Page $page): void
    {
        $this->pages[] = $page;
    }

    public function addLink(string $link, string $identifier, string $prefix = null): void
    {
        $this->links->link($link, $identifier, $prefix);
    }

    public function addMenuItem(MenuItem $menuItem): void
    {
        $this->menuItems[] = $menuItem;
    }

    public function linkResolver(Page $active): LinkResolver
    {
        return $this->links->forPrefix((string) $active->prefix());
    }

    public function menuForPage(Page $active): array
    {
        return array_map(function (MenuItem $item) use ($active) {
            return $item->activate($active);
        }, $this->menuItems);
    }

    public function each(callable $callback): void
    {
        array_walk($this->pages, $callback);
    }
}
