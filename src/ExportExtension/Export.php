<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\ExportExtension;

use Dkplus\LivingDocs\PagesExtension\ProcessedPage;
use Dkplus\LivingDocs\PagesExtension\ProcessedPages;
use function array_combine;
use function array_keys;
use function array_map;
use function array_walk;
use function iterator_to_array;
use function var_dump;

class Export
{
    /** @var string */
    private $target;

    /** @var string[] */
    private $pages;

    /** @var mixed[] */
    private $menu;

    /** @var array */
    private $extra;

    /** @var MenuItem[] */
    private $buildMenu;

    public function __construct(string $target, array $pages, array $menu, array $extra)
    {
        $this->target = $target;
        $this->pages = $pages;
        $this->menu = $menu;
        $this->extra = $extra;
    }

    /**
     * @param ProcessedPages $pages
     * @param PageRenderer $renderer
     * @return string[]
     */
    public function render(ProcessedPages $pages, PageRenderer $renderer): array
    {
        $pages = $pages->matching($this->pages);
        $renderer->start(iterator_to_array($pages));
        foreach ($pages as $id => $page) {
            $renderer->render($id, $page, array_merge($this->extra, ['menu' => $this->menu($pages)]));
        }
        $pages = $renderer->finish();
        $pageFileNames = array_keys($pages);
        array_walk($pageFileNames, function (&$fileName) {
            $fileName = $this->target . '/' . $fileName;
        });
        return array_combine($pageFileNames, $pages);
    }

    private function menu(ProcessedPages $pages): array
    {
        if ($this->buildMenu === null) {
            $this->buildMenu = array_map(function (array $menuItem) use ($pages) {
                return $this->createMenuItem($menuItem, $pages);
            }, $this->menu);
        }
        return $this->buildMenu;
    }

    private function createMenuItem(array $menuItem, ProcessedPages $pages): MenuItem
    {
        /* @var $pageId string */
        $pageId = $menuItem['page'] ?? '';
        $label = $menuItem['label'] ?? '';
        if ($pageId && ! $label) {
            $label = $pages->withId($pageId)->getTitle();
        }
        $children = [];
        /* @var $eachPage ProcessedPage */
        foreach ($pages->matching($menuItem['children']) as $eachId => $eachPage) {
            if ($pageId === $eachId) {
                continue;
            }
            $children[] = new MenuItem($eachPage->getTitle(), $eachId, []);
        }
        return new MenuItem($label, $pageId, $children);
    }
}
