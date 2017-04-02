<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use Dkplus\LivingDocs\Rendering\Page\Page;
use Dkplus\LivingDocs\Rendering\Page\Pages;
use Twig_Environment;
use function var_dump;

class PageRenderer
{
    /** @var Twig_Environment */
    private $twig;
    /** @var LinkExtension */
    private $linkExtension;

    public function __construct(Twig_Environment $twig)
    {
        $this->linkExtension = new LinkExtension();
        $this->twig = $twig;
        $this->twig->addExtension($this->linkExtension);
    }

    public function render(Page $page, Pages $pages): string
    {
        $this->linkExtension->setLinkResolver($pages->linkResolver($page));
        //var_dump(array_merge($page->context(), ['menu' => $pages->menuForPage($page)]));
        return $this->twig->render(
            $page->template(),
            array_merge($page->context(), ['menu' => $pages->menuForPage($page)])
        );
    }
}
