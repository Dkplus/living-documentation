<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\SourceCodeExtension;

use Assert\Assert;

class LineRange
{
    /** @var int */
    private $start;

    /** @var int */
    private $end;

    public static function multiLine(int $start, int $end): self
    {
        return new self($start, $end);
    }

    private function __construct(int $start, int $end)
    {
        Assert::that($start)->lessOrEqualThan($end);
        $this->start = $start;
        $this->end = $end;
    }

    /** @return int */
    public function start(): int
    {
        return $this->start;
    }

    /** @return int */
    public function end(): int
    {
        return $this->end;
    }
}
