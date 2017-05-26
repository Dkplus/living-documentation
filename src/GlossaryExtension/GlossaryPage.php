<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\GlossaryExtension;

use Dkplus\LivingDocumentation\PagesExtension\Page;

class GlossaryPage implements Page
{
    /** @var string */
    private $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
