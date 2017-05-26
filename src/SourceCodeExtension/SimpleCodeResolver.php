<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension;

use Dkplus\LivingDocumentation\Extension\RelativePathCalculator;
use ReflectionClass;
use ReflectionFunction;
use RuntimeException;
use function array_map;
use function file_get_contents;
use function is_dir;
use function mb_strlen;
use function usort;

final class SimpleCodeResolver implements CodeResolver
{
    /** @var array */
    private $directories;

    /** @var RelativePathCalculator */
    private $pathCalculator;

    public function __construct(array $directories, RelativePathCalculator $pathCalculator)
    {
        $this->directories = array_map(function (string $directory) {
            if (! is_dir($directory)) {
                throw new RuntimeException("$directory is no directory");
            }
            return $directory;
        }, $directories);
        $this->pathCalculator = $pathCalculator;
    }

    public function resolveClassCode(string $className): CodeSnippet
    {
        $reflection = new ReflectionClass($className);
        return $this->createSnippet($reflection->getFileName(), $reflection->getStartLine(), $reflection->getEndLine());
    }

    private function createSnippet(string $file, int $startPos, int $endPos): CodeSnippet
    {
        $code = implode(
            "\n",
            array_slice(
                explode("\n", file_get_contents($file)),
                $startPos - 1,
                $endPos - $startPos + 1
            )
        );
        $fileName = $file;
        $possiblePaths = array_map(function (string $directory) use ($fileName) {
            return $this->pathCalculator->relativeToDirectory($fileName, $directory);
        }, $this->directories);
        usort($possiblePaths, function (string $pathOne, string $pathTwo) {
            $parentCountOne = mb_substr_count($pathOne, '..');
            $parentCountTwo = mb_substr_count($pathTwo, '..');
            if ($parentCountOne === $parentCountTwo) {
                return mb_strlen($pathOne) <=> mb_strlen($pathTwo);
            }
            return $parentCountOne <=> $parentCountTwo;
        });
        if (count($possiblePaths) > 0) {
            $fileName = current($possiblePaths);
        }
        return CodeSnippet::consecutiveCode($code, LineRange::multiLine($startPos, $endPos), $fileName);
    }

    public function resolveMethod(string $className, string $method): CodeSnippet
    {
        $reflection = (new ReflectionClass($className))->getMethod($method);
        return $this->createSnippet($reflection->getFileName(), $reflection->getStartLine(), $reflection->getEndLine());
    }

    public function resolveFunction(string $function): CodeSnippet
    {
        $reflection = new ReflectionFunction($function);
        return $this->createSnippet($reflection->getFileName(), $reflection->getStartLine(), $reflection->getEndLine());
    }
}
