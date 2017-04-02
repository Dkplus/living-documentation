<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use Dkplus\LivingDocs\Documentation;

interface Converter
{
    public function convert(Documentation $documentation, Configuration $pages, Converter $converter): void;
}
