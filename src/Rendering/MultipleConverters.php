<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use Dkplus\LivingDocs\Documentation;

class MultipleConverters implements Converter
{
    /** @var Converter[] */
    private $converters;

    public function __construct(Converter ...$converters)
    {
        $this->converters = $converters;
    }

    public function convert(Documentation $documentation, Configuration $pages, Converter $converter): void
    {
        foreach ($this->converters as $each) {
            $each->convert($documentation, $pages, $this);
        }
    }
}
