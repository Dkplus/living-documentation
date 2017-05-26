<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SightseeingExtension;

use Dkplus\LivingDocumentation\Annotation\FactoryMethod;
use Dkplus\LivingDocumentation\SourceCodeExtension\CodeResolver;

interface PointOfInterestDescription
{
    /** @FactoryMethod */
    public function finish(CodeResolver $resolver): PointOfInterest;
}
