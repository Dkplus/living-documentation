<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

use function array_merge;
use function array_unshift;

/**
 * @internal
 */
class Spouses
{
    /** @var ?Spouses */
    private $parents;

    /** @var Spouses[] */
    private $children = [];

    /** @var Node[] */
    private $spouses;

    public static function progenitor(Node $founder): self
    {
        return new self($founder, null);
    }

    public static function adopted(Spouses $parents, Node $adopted): self
    {
        return new self($adopted, $parents);
    }

    private function __construct(Node $founder, ?Spouses $parents)
    {
        $this->parents = $parents;
        $this->spouses = [$founder];
    }

    public function marry(Node $node): void
    {
        $this->spouses[] = $node;
    }

    public function adopt(Node $node): Spouses
    {
        $child = Spouses::adopted($this, $node);
        $this->children[] = $child;
        return $child;
    }

    public function all(): Nodes
    {
        return new Nodes(...$this->spouses);
    }

    /** @return Spouses[] */
    public function ancestors(): array
    {
        if (! $this->parents) {
            return [];
        }
        $ancestors = $this->parents->ancestors();
        array_unshift($ancestors, $this->parents);
        return $ancestors;
    }

    /** @return Spouses[] */
    public function children(): array
    {
        return $this->children;
    }

    /** @return Spouses[] */
    public function descendants(): array
    {
        $descendants = [];
        $nextDescendants = $this->children();
        while ($nextDescendants) {
            $newDescendants = [];
            foreach ($nextDescendants as $each) {
                $descendants[] = $each;
                $newDescendants[] = $each->children();
            }
            $nextDescendants = array_merge([], ...$newDescendants);
        }
        return $descendants;
    }
}
