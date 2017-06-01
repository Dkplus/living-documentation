<?php
declare(strict_types=1);

namespace test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures;

use PhpParser\Node;

class ClassWithMultipleProperties
{
    /** @var OneInterface */
    public static $staticProperty;

    /**
     * @var OneTrait
     */
    private $trait;

    /**
     * @var array|InterfaceExtendingTwoInterfaces[]|ClassWithMultipleProperties[]
     */
    private $multipleArrayTypes;

    /** @var Node */
    public $externalClass;

    /** @var int */
    protected $ignoredBuiltInType;
}
