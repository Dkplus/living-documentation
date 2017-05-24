<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\MarkdownExtension;

use Dkplus\LivingDocs\PagesExtension\Page;
use function file_get_contents;

class MarkdownPage implements Page
{
    /** @var string */
    private $title;

    /** @var string */
    private $sourcePath;

    public function __construct(string $title, string $sourcePath)
    {
        $this->title = $title;
        $this->sourcePath = $sourcePath;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMarkdownText(): string
    {
        return file_get_contents($this->sourcePath);
    }
}
