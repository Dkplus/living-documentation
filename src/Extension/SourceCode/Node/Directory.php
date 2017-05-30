<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\SourceTree\Node;

class Directory implements Node
{
    /** @var string */
    private $path;

    public function __construct(string $filePath)
    {
        $this->path = $filePath;
    }

    public function path(): string
    {
        return $this->path;
    }
}
