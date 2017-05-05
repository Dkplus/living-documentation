<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SightseeingExtension;

use Dkplus\LivingDocs\Annotation\CoreConcept;

/**
 * A point of interest is one code snippet that is interesting for the user within a tour.
 *
 * @CoreConcept
 */
class PointOfInterest
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $fileName;

    /** @var string */
    private $code;

    /** @var string */
    private $codeName;

    public static function withCode(
        string $name,
        string $description,
        string $fileName,
        string $codeName,
        string $code
    ): self {
        return new self($name, $description, $fileName, $codeName, $code);
    }

    public static function withoutCode(
        string $name,
        string $description,
        string $fileName,
        string $codeName
    ): self {
        return new self($name, $description, $fileName, $codeName, '');
    }

    private function __construct(string $name, string $description, string $fileName, string $codeName, string $code)
    {
        $this->name = $name;
        $this->description = $description;
        $this->fileName = $fileName;
        $this->codeName = $codeName;
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCodeName(): string
    {
        return $this->codeName;
    }
}
