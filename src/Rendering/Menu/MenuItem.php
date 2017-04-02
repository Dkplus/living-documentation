<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering\Menu;

use Dkplus\LivingDocs\Rendering\Page\Page;

interface MenuItem
{
    public function getLabel(): string;

    public function getLink(): string;

    /** @return self[] */
    public function getChildren(): array;

    public function isActive(): bool;

    public function activate(Page $page): MenuItem;
}
