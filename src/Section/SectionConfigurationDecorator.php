<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Section;

use Dkplus\LivingDocs\Rendering\Configuration;
use Dkplus\LivingDocs\Rendering\Menu\MenuItem;
use Dkplus\LivingDocs\Rendering\Page\Page;

class SectionConfigurationDecorator implements Configuration
{
    /** @var Configuration */
    private $decorated;

    /** @var string */
    private $prefix;

    public function __construct(Configuration $decorated, string $prefix)
    {
        $this->decorated = $decorated;
    }

    public function addPage(Page $page): void
    {
        $this->decorated->addPage(new SectionPageDecorator($this->prefix, $page));
    }

    public function addMenuItem(MenuItem $menuItem): void
    {
        $this->decorated->addMenuItem($menuItem);
    }

    public function addLink(string $link, string $identifier, string $prefix = null): void
    {
        $prefix = $prefix ? $this->prefix . '/' . $prefix : $this->prefix;
        $this->decorated->addLink($this->prefix . '/' . $link, $identifier, $prefix);
    }
}
