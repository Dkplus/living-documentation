<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

class ProcessedPage
{
    /** @var string */
    private $title;

    /** @var string */
    private $template;

    /** @var array */
    private $context;

    public function __construct(string $title, string $template, array $context)
    {
        $this->title = $title;
        $this->template = $template;
        $this->context = $context;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
