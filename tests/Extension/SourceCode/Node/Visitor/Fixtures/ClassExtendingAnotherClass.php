<?php
declare(strict_types=1);

namespace test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures;

use stdClass;

class ClassExtendingAnotherClass extends stdClass
{
    use OneTrait;
}
