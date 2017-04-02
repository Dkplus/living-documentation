<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Sightseeing;

use Dkplus\LivingDocs\CodeResolver;
use Dkplus\LivingDocs\CodeSnippet;
use function implode;
use function is_array;

class FunctionPointOfInterestDescription implements PointOfInterestDescription
{
    /** @var callable */
    private $function;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $includeCode;

    public static function withCode(callable $function, string $name, string $description): self
    {
        $result = new self($function, $name, $description);
        $result->includeCode = true;
        return $result;
    }

    public static function withoutCode(callable $function, string $name, string $description): self
    {
        $result = new self($function, $name, $description);
        $result->includeCode = false;
        return $result;
    }

    private function __construct(callable $function, string $name, string $description)
    {
        $this->function = $function;
        $this->name = $name;
        $this->description = $description;
    }

    public function finish(CodeResolver $resolver): PointOfInterest
    {
        $codeSnippet = $resolver->resolveFunction($this->function);
        $funcName = is_array($this->function) ? implode('::', $this->function) : $this->function;
        if (! $this->includeCode) {
            return PointOfInterest::withoutCode($this->name, $this->description, $codeSnippet->fileName(), $funcName);
        }
        return PointOfInterest::withCode(
            $this->name,
            $this->description,
            $codeSnippet->fileName(),
            $funcName,
            $codeSnippet->code()
        );
    }
}
