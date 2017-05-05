<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Extension;

use function array_shift;
use function explode;

class RelativePathCalculator
{
    public function relativeToFile(string $path, string $reference): string
    {
        $referenceParts = explode('/', $reference);
        array_pop($referenceParts);
        return $this->relativeToDirectory($path, implode('/', $referenceParts));
    }

    public function relativeToDirectory(string $path, string $reference): string
    {
        $resultParts = [];
        $parts = explode('/', $path);
        $referenceParts = explode('/', $reference);

        // ignore filename for now
        $fileName = array_pop($parts);

        do {
            $part = array_shift($parts);
            $referencePart = array_shift($referenceParts);
        } while ($part === $referencePart && $part !== null);

        while ($referencePart !== null) {
            $resultParts[] = '..';
            $referencePart = array_shift($referenceParts);
        }

        $resultParts = array_merge($resultParts, $part !== null ? [$part] : [], $parts);

        $resultParts[] = $fileName;
        return implode('/', $resultParts);
    }
}
