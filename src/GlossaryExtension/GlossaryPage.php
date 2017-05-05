<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\GlossaryExtension;

use Dkplus\LivingDocs\PagesExtension\Page;

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
