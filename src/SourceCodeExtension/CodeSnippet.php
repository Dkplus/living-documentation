<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension;

class CodeSnippet
{
    /** @var string */
    private $code;

    /** @var LineRange */
    private $lines;

    /** @var string */
    private $fileName;

    public static function consecutiveCode(string $code, LineRange $lines, string $fileName): CodeSnippet
    {
        return new self($code, $lines, $fileName);
    }

    private function __construct(string $code, LineRange $lines, string $fileName)
    {
        $this->code = $code;
        $this->lines = $lines;
        $this->fileName = $fileName;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function lines(): LineRange
    {
        return $this->lines;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }
}
