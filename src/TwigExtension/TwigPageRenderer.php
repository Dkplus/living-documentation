<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\TwigExtension;

use function array_merge;
use Dkplus\LivingDocs\ExportExtension\PageRenderer;
use Dkplus\LivingDocs\PagesExtension\ProcessedPage;
use InvalidArgumentException;
use function method_exists;
use Twig_Environment;
use Twig_Extension;
use function var_dump;

class TwigPageRenderer implements PageRenderer
{
    /** @var Twig_Environment */
    private $twig;

    /** @var PathResolver */
    private $pathResolver;

    /** @var string[] */
    private $renderedPages;

    /** @var ProcessedPage[] */
    private $pagesById;

    /** @var array */
    private $extra = [];

    public function __construct(Twig_Environment $twig, PathResolver $pathResolver)
    {
        $this->twig = $twig;
        $this->pathResolver = $pathResolver;
    }

    public function addTwigExtension(Twig_Extension $extension): void
    {
        if (method_exists($extension, 'setPageRenderer')) {
            $extension->setPageRenderer($this);
        }
        $this->twig->addExtension($extension);
    }

    public function start(array $pages): void
    {
        $this->renderedPages = [];
        $this->pagesById = $pages;
        $this->pathResolver->restrictResolvableIds(array_keys($pages));
    }

    public function render(string $id, ProcessedPage $page, array $extra): void
    {
        $this->extra = $extra;
        $this->pathResolver->setBasePathId($id);
        $this->renderedPages[$this->pathResolver->resolve($id, true)] = $this->twig->render(
            $page->getTemplate() . '.html.twig',
            array_merge($extra, $page->getContext())
        );
    }

    public function renderBodyOfPage(string $id): string
    {
        if (! isset($this->pagesById[$id])) {
            throw new InvalidArgumentException("There is no page with id $id");
        }
        $page = $this->pagesById[$id];
        $template = $this->twig->resolveTemplate($page->getTemplate() . '.html.twig');
        $context = array_merge($this->extra, $page->getContext());
        return $template->renderBlock('body', $context);
    }

    public function finish(): array
    {
        return $this->renderedPages;
    }
}
