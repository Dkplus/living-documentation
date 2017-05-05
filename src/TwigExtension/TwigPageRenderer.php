<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\TwigExtension;

use Dkplus\LivingDocs\ExportExtension\PageRenderer;
use Dkplus\LivingDocs\PagesExtension\ProcessedPage;
use Twig_Environment;

class TwigPageRenderer implements PageRenderer
{
    /** @var Twig_Environment */
    private $twig;

    /** @var PathResolver */
    private $pathResolver;

    /** @var string[] */
    private $renderedPages;

    public function __construct(Twig_Environment $twig, PathResolver $pathResolver)
    {
        $this->twig = $twig;
        $this->pathResolver = $pathResolver;
    }

    public function start(array $pageIds): void
    {
        $this->renderedPages = [];
        $this->pathResolver->restrictResolvableIds($pageIds);
    }

    public function render(string $id, ProcessedPage $page, array $extra): void
    {
        $this->pathResolver->setBasePathId($id);
        $this->renderedPages[$this->pathResolver->resolve($id, true)] = $this->twig->render(
            $page->getTemplate() . '.html.twig',
            array_merge($extra, $page->getContext())
        );
    }

    public function finish(): array
    {
        return $this->renderedPages;
    }
}
