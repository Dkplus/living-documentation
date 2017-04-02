<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\BetterReflection;

use BetterReflection\Reflector\ClassReflector;
use BetterReflection\Reflector\FunctionReflector;
use Dkplus\LivingDocs\CodeResolver;
use Dkplus\LivingDocs\CodeSnippet;
use Dkplus\LivingDocs\LineRange;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\PrettyPrinterAbstract;
use function is_array;
use function is_string;

final class BetterReflectionCodeResolver implements CodeResolver
{
    /** @var FunctionReflector */
    private $functionReflector;

    /** @var ClassReflector */
    private $classReflector;

    /** @var PrettyPrinterAbstract */
    private $printer;

    public function __construct(
        ClassReflector $classReflector,
        FunctionReflector $functionReflector,
        PrettyPrinterAbstract $printer
    ) {
        $this->classReflector = $classReflector;
        $this->functionReflector = $functionReflector;
        $this->printer = $printer;
    }

    public function resolveClassCode(string $className): CodeSnippet
    {
        $reflection = $this->classReflector->reflect($className);
        return CodeSnippet::consecutiveCode(
            $reflection->getLocatedSource()->getSource(),
            //$this->code($reflection->getAst()->stmts),
            LineRange::multiLine($reflection->getStartLine(), $reflection->getEndLine()),
            $reflection->getFileName()
        );
    }

    /**
     * @param Node[] $statements
     * @return string
     */
    private function code(array $statements): string
    {
        return $this->printer->prettyPrint($statements);
    }

    public function resolveFunction(callable $function): CodeSnippet
    {
        if (is_string($function)) {
            $reflection = $this->functionReflector->reflect($function);
            return CodeSnippet::consecutiveCode(
                $this->code($reflection->getAst()->getStmts()),
                LineRange::multiLine($reflection->getStartLine(), $reflection->getEndLine()),
                $reflection->getFileName()
            );
        }
        if (is_array($function)) {
            $class = $this->classReflector->reflect($function[0]);
            $method = $class->getMethod($function[1]);
            return CodeSnippet::consecutiveCode(
                $this->code($method->getAst()->getStmts()),
                LineRange::multiLine($method->getStartLine(), $method->getEndLine()),
                $method->getFileName()
            );
        }
        throw new InvalidArgumentException('Closures are not yet supported');
    }
}
