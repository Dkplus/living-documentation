<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Sightseeing;

use Dkplus\LivingDocs\CodeResolver;
use Dkplus\LivingDocs\Documentation;
use Dkplus\LivingDocs\Rendering\Configuration;
use Dkplus\LivingDocs\Rendering\Converter;
use Dkplus\LivingDocs\Rendering\Menu\InactiveMenuItem;
use Dkplus\LivingDocs\Rendering\SinglePage;

class SightseeingConverter implements Converter
{
    /** @var CodeResolver */
    private $codeResolver;

    public function __construct(CodeResolver $codeResolver)
    {
        $this->codeResolver = $codeResolver;
    }

    public function convert(Documentation $documentation, Configuration $pages, Converter $converter): void
    {
        if (! $documentation instanceof TourDescription) {
            return;
        }
        $tour = $documentation->finish($this->codeResolver);
        $pages->addLink($tour->getName() . '.html', $tour->getName());
        $pages->addMenuItem(InactiveMenuItem::page($tour->getName(), $tour->getName() . '.html'));
        $pages->addPage(new SinglePage(
            $tour->getName(),
            $tour->getName() . '.html',
            'tour.html.twig',
            ['tour' => $tour]
        ));
    }
}
