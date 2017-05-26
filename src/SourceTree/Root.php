<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

final class Root implements Node
{
    /**
     * @var string
     */
    private $path;

    public function __construct(string $directoryPath)
    {
        $this->path = $directoryPath;
    }

    public function isDescendantOf(Node $anotherNode): bool
    {
        return false;
    }

    public function filePath(): string
    {
        return $this->path;
    }

    public function name(): string
    {
        return $this->path;
    }

    public function findAncestorOfClass(string $class): Node
    {
        throw NodeNotFound::asAncestorOfClass($class, $this);
    }
}
