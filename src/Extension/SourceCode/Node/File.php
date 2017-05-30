<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\SourceTree\Node;
use function pathinfo;
use const PATHINFO_EXTENSION;

class File implements Node
{
    /** @var string */
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function path(): string
    {
        return $this->filePath;
    }

    public function extension(): string
    {
        return pathinfo($this->filePath, PATHINFO_EXTENSION);
    }
}
