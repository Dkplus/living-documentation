<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use Dkplus\LivingDocs\Rendering\Menu\MenuItem;
use Dkplus\LivingDocs\Rendering\Page\Page;

interface Configuration
{
    public function addLink(string $link, string $identifier, string $prefix = null): void;
    public function addPage(Page $page): void;
    public function addMenuItem(MenuItem $menuItem): void;
}
