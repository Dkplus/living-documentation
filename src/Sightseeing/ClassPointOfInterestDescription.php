<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Sightseeing;

use Dkplus\LivingDocs\CodeResolver;
use Dkplus\LivingDocs\CodeSnippet;

class ClassPointOfInterestDescription implements PointOfInterestDescription
{
    /** @var string */
    private $class;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $includeCode;

    public static function withoutCode(string $class, string $name, string $description): self
    {
        $result = new self($class, $name, $description);
        $result->includeCode = false;
        return $result;
    }

    public static function withCode(string $class, string $name, string $description): self
    {
        $result = new self($class, $name, $description);
        $result->includeCode = true;
        return $result;
    }

    private function __construct(string $class, string $name, string $description)
    {
        $this->class = $class;
        $this->name = $name;
        $this->description = $description;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function finish(CodeResolver $resolver): PointOfInterest
    {
        $codeSnippet = $resolver->resolveClassCode($this->class);
        if (! $this->includeCode) {
            return PointOfInterest::withoutCode(
                $this->name,
                $this->description,
                $codeSnippet->fileName(),
                $this->class
            );
        }
        return PointOfInterest::withCode(
            $this->name,
            $this->description,
            $codeSnippet->fileName(),
            $this->class,
            $codeSnippet->code()
        );
    }
}
