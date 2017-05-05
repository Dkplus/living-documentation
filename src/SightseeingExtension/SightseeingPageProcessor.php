<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SightseeingExtension;

use Dkplus\LivingDocs\PagesExtension\Page;
use Dkplus\LivingDocs\PagesExtension\PageProcessor;
use Dkplus\LivingDocs\PagesExtension\ProcessedPage;
use Dkplus\LivingDocs\SourceCodeExtension\CodeResolver;
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
