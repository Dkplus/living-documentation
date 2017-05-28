<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceTree;

/**
 * Top node of the analyzed project.
 */
final class Project extends Node
{
    public function __construct(FamilyTree $familyTree)
    {
        parent::__construct($familyTree);
    }

    public function name(): string
    {
        return self::class;
    }
}
