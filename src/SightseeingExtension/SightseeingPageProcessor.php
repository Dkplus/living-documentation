<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SightseeingExtension;

use Dkplus\LivingDocumentation\PagesExtension\Page;
use Dkplus\LivingDocumentation\PagesExtension\PageProcessor;
use Dkplus\LivingDocumentation\PagesExtension\ProcessedPage;
use Dkplus\LivingDocumentation\SourceCodeExtension\CodeResolver;
use InvalidArgumentException;
use RuntimeException;
use function get_class;

class SightseeingPageProcessor implements PageProcessor
{
    /** @var CodeResolver */
    private $codeResolver;

    public function __construct(CodeResolver $codeResolver)
    {
        $this->codeResolver = $codeResolver;
    }

    public function preProcess(Page $page): void
    {
    }

    public function process(Page $page): ProcessedPage
    {
        if (! $page instanceof SightseeingPage) {
            throw new InvalidArgumentException(get_class($page));
        }
        $tourDescription = include $page->getDefinitionPath();
        if (! $tourDescription instanceof TourDescription) {
            throw new RuntimeException('Expected a ' . TourDescription::class . ' from ' . $page->getDefinitionPath());
        }
        $tour = $tourDescription->finish($this->codeResolver);
        return new ProcessedPage(
            $tour->getName(),
            'tour',
            ['tour' => $tour]
        );
    }
}
