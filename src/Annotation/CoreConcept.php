<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target("ALL")
 */
class CoreConcept
{
    /**
     * @var string
     * @Annotation\Required()
     */
    public $description;
}
