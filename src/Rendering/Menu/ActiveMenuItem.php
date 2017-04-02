<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering\Menu;

use Dkplus\LivingDocs\Rendering\Page\Page;

class ActiveMenuItem implements MenuItem
{
    /** @var InactiveMenuItem */
    private $decorated;

    public function __construct(MenuItem $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getLabel(): string
    {
        return $this->decorated->getLabel();
    }

    public function getLink(): string
    {
        return $this->decorated->getLink();
    }

    public function getChildren(): array
    {
        return $this->decorated->getChildren();
    }

    public function isActive(): bool
    {
        return true;
    }

    public function activate(Page $page): MenuItem
    {
        return $this;
    }
}
