<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SightseeingExtension;

use Dkplus\LivingDocumentation\SourceCodeExtension\CodeResolver;

class MethodPointOfInterestDescription implements PointOfInterestDescription
{
    /** @var string */
    private $className;

    /** @var string */
    private $method;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $includeCode;

    public static function withCode(string $className, string $method, string $name, string $description): self
    {
        $result = new self($className, $method, $name, $description);
        $result->includeCode = true;
        return $result;
    }

    public static function withoutCode(string $className, string $method, string $name, string $description): self
    {
        $result = new self($className, $method, $name, $description);
        $result->includeCode = false;
        return $result;
    }

    private function __construct(string $className, string $method, string $name, string $description)
    {
        $this->className = $className;
        $this->method = $method;
        $this->name = $name;
        $this->description = $description;
    }

    public function finish(CodeResolver $resolver): PointOfInterest
    {
        $codeSnippet = $resolver->resolveMethod($this->className, $this->method);
        $funcName = $this->className . '::' . $this->method;
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
