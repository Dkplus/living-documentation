<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\SourceTree\Node;

class Property implements Node
{
    /** @var string */
    private $className;

    /** @var string */
    private $name;

    public function __construct(string $className, string $name)
    {
        $this->className = $className;
        $this->name = $name;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function name(): string
    {
        return $this->name;
    }
}
