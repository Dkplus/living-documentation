<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Sightseeing;

class Tour
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var PointOfInterest[] */
    private $pointsOfInterest;

    /**
     * Tour constructor.
     * @param string $name
     * @param string $description
     * @param PointOfInterest[] $pointsOfInterest
     */
    public function __construct(string $name, string $description, PointOfInterest ...$pointsOfInterest)
    {
        $this->name = $name;
        $this->description = $description;
        $this->pointsOfInterest = $pointsOfInterest;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /** @return PointOfInterest[] */
    public function getPointsOfInterest(): array
    {
        return $this->pointsOfInterest;
    }
}
