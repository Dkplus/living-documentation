<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SightseeingExtension;

use Dkplus\LivingDocs\Annotation\FactoryMethod;
use Dkplus\LivingDocs\SourceCodeExtension\CodeResolver;

interface PointOfInterestDescription
{
    /** @FactoryMethod */
    public function finish(CodeResolver $resolver): PointOfInterest;
}
