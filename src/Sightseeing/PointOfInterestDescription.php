<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Sightseeing;

use Dkplus\LivingDocs\CodeResolver;

interface PointOfInterestDescription
{
    public function finish(CodeResolver $resolver): PointOfInterest;
}
