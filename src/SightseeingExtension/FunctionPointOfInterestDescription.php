<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SightseeingExtension;

use Dkplus\LivingDocs\SourceCodeExtension\CodeResolver;

class FunctionPointOfInterestDescription implements PointOfInterestDescription
{
    /** @var string */
    private $function;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $includeCode;

    public static function withCode(string $function, string $name, string $description): self
    {
        $result = new self($function, $name, $description);
        $result->includeCode = true;
        return $result;
    }

    public static function withoutCode(string $function, string $name, string $description): self
    {
        $result = new self($function, $name, $description);
        $result->includeCode = false;
        return $result;
    }

    private function __construct(string $function, string $name, string $description)
    {
        $this->function = $function;
        $this->name = $name;
        $this->description = $description;
    }

    public function finish(CodeResolver $resolver): PointOfInterest
    {
        $codeSnippet = $resolver->resolveFunction($this->function);
        $funcName = $this->function;
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
