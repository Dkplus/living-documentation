<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Section;

use Dkplus\LivingDocs\Documentation;
use Dkplus\LivingDocs\Rendering\Configuration;
use Dkplus\LivingDocs\Rendering\Converter;

class SectionConverter implements Converter
{
    public function convert(Documentation $documentation, Configuration $pages, Converter $converter): void
    {
        if (! $documentation instanceof Section) {
            return;
        }
        $pages = $documentation->decoratePages($pages);
        foreach ($documentation->getParts() as $each) {
            $converter->convert($each, $pages, $converter);
        }
    }
}
