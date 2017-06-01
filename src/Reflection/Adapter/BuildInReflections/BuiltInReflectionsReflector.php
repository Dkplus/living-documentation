<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Adapter\BuildInReflections;

use Assert\Assert;
use Dkplus\LivingDocumentation\Reflection\Class_;
use Dkplus\LivingDocumentation\Reflection\ClassAlike;
use Dkplus\LivingDocumentation\Reflection\Method;
use Dkplus\LivingDocumentation\Reflection\Property;
use Dkplus\LivingDocumentation\Reflection\Reflector;
use Dkplus\LivingDocumentation\Reflection\StatefulClassAlike;
use Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing\ClassCollectingVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use RuntimeException;
use function array_map;
use function file_get_contents;

class BuiltInReflectionsReflector implements Reflector
{
    /** @var Parser */
    private $parser;

    /* @var NodeTraverser */
    private $traverser;

    /** @var ClassCollectingVisitor */
    private $classCollector;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
        $this->traverser->addVisitor($this->classCollector = new ClassCollectingVisitor());
    }

    public function reflectFile(string $fileName): array
    {
        Assert::that($fileName)->file();
        $this->traverser->traverse($this->parser->parse(file_get_contents($fileName)));
        return array_map([$this, 'reflectClass'], $this->classCollector->getClassNames());
    }

    public function reflectClass(string $fcqn): ClassAlike
    {
        $reflecton = new ReflectionClass($fcqn);
        return new Class_(
            $reflecton->name,
        );
    }

    public function reflectMethod(string $fcqn, string $method): Method
    {
        return $this->reflectClass($fcqn)->method($method);
    }

    public function reflectProperty(string $fcqn, string $property): Property
    {
        $class = $this->reflectClass($fcqn);
        if (! $class instanceof StatefulClassAlike) {
            throw new RuntimeException();
        }
        return $class->property($property);
    }
}
