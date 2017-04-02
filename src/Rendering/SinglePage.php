<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use Dkplus\LivingDocs\Rendering\Page\Page;

class SinglePage implements Page
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $fileName;

    /** @var string */
    private $template;

    /** @var array*/
    private $context;

    public function __construct(string $identifier, string $fileName, string $template, array $context)
    {
        $this->identifier = $identifier;
        $this->fileName = $fileName;
        $this->template = $template;
        $this->context = $context;
    }

    public function prefix(): ?string
    {
        return null;
    }

    public function identifier(): string
    {
        return $this->identifier;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function template(): string
    {
        return $this->template;
    }

    public function context(): array
    {
        return $this->context;
    }
}
