<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering\Page;

use Dkplus\LivingDocs\Rendering\LinkResolver;

interface Pages
{
    public function menuForPage(Page $active): array;
    public function linkResolver(Page $active): LinkResolver;
}
