<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

interface Node
{
    public function isDescendantOf(Node $anotherNode): bool;
    public function name(): string;
    public function filePath(): string;
    /** @throws NodeNotFound */
    public function findAncestorOfClass(string $class): Node;
}
