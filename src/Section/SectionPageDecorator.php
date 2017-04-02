<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Section;

use Dkplus\LivingDocs\Rendering\Page\Page;

class SectionPageDecorator implements Page
{
    /** @var string */
    private $sectionIdentifier;

    /** @var Page */
    private $decorated;

    public function __construct(string $sectionIdentifier, Page $decorated)
    {
        $this->sectionIdentifier = $sectionIdentifier;
        $this->decorated = $decorated;
    }

    public function prefix(): string
    {
        if ($this->decorated->prefix() === null) {
            return $this->sectionIdentifier;
        }
        return $this->sectionIdentifier . '/' . $this->decorated->prefix();
    }

    public function identifier(): string
    {
        return $this->decorated->identifier();
    }

    public function fileName(): string
    {
        return $this->sectionIdentifier . '/' . $this->decorated->fileName();
    }

    public function template(): string
    {
        return $this->decorated->template();
    }

    public function context(): array
    {
        return $this->decorated->context();
    }
}
