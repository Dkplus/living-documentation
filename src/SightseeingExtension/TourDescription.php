<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SightseeingExtension;

use Assert\Assert;
use Dkplus\LivingDocs\SourceCodeExtension\CodeResolver;
use InvalidArgumentException;
use function array_map;

class TourDescription
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var PointOfInterestDescription[] */
    private $pointsOfInterests = [];

    public function name(string $name): void
    {
        $this->name = $name;
    }

    public function describe(string $description): void
    {
        $this->description = $description;
    }

    public function appendPointOfInterest(PointOfInterestDescription $pointOfInterest): void
    {
        $this->pointsOfInterests[] = $pointOfInterest;
    }

    public function addPointOfInterest(PointOfInterestDescription $pointOfInterest, int $position): void
    {
        Assert::that($position)->greaterOrEqualThan(0);
        if (isset($this->pointsOfInterests[$position])) {
            throw new InvalidArgumentException('There is already a point of interest at position ' . $position);
        }
        $this->pointsOfInterests[$position] = $pointOfInterest;
    }

    public function finish(CodeResolver $resolver): Tour
    {
        $pointsOfInterest = array_map(function (PointOfInterestDescription $pointOfInterest) use ($resolver) {
            return $pointOfInterest->finish($resolver);
        }, $this->pointsOfInterests);
        return new Tour($this->name, $this->description, ...$pointsOfInterest);
    }
}
