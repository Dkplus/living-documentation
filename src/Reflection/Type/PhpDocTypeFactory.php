<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Reflection\Type;

use phpDocumentor\Reflection\Type as PhpDocumentorType;
use Dkplus\Reflections\Reflector;
use phpDocumentor\Reflection\Types\Mixed;
use Traversable;

class PhpDocTypeFactory implements TypeFactory
{
    /** @var TypeFactory */
    private $decorated;

    public function __construct(TypeFactory $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(Reflector $reflector, PhpDocumentorType $type, array $phpDocTypes, bool $nullable): Type
    {
        if (count($phpDocTypes) > 1 || substr(current($phpDocTypes), -2) === '[]') {
            $nonTraversableDocTypes = array_filter($phpDocTypes, function (string $type) {
                return substr($type, -2) !== '[]';
            });
            $traversableDocTypes = array_diff($phpDocTypes, $nonTraversableDocTypes);

            if (count($traversableDocTypes) > 0 && count($nonTraversableDocTypes) <= 1) {
                $traversableTypes = array_map(
                    function (string $traversablePhpDocType) use ($reflector) {
                        return $this->create($reflector, new Mixed(), [substr($traversablePhpDocType, 0, -2)], false);
                    },
                    $traversableDocTypes
                );
                $decoratedType = count($traversableTypes) > 1
                    ? new ComposedType(...$traversableTypes)
                    : current($traversableTypes);
                if (count($nonTraversableDocTypes) === 0) {
                    return new IterableType($decoratedType);
                }
                if (current($nonTraversableDocTypes) === 'array') {
                    return new ArrayType($decoratedType);
                }

                $nonTraversableType = $this->create($reflector, new Mixed(), $nonTraversableDocTypes, $nullable);
                if ($nonTraversableType instanceof ClassType
                    && $nonTraversableType->reflection()->implementsInterface(Traversable::class)
                ) {
                    return new CollectionType($nonTraversableType, $decoratedType);
                }
            }

            return new ComposedType(...array_map(function (string $phpDocType) use ($type, $nullable, $reflector) {
                return $this->create($reflector, $type, [$phpDocType], $nullable);
            }, $phpDocTypes));
        }
        if (count($phpDocTypes) === 1) {
            switch (current($phpDocTypes)) {
                case 'string':
                    return new StringType();
                case 'integer':
                case 'int':
                    return new IntegerType();
                case 'float':
                case 'double':
                    return new FloatType();
                case 'boolean':
                case 'bool':
                case 'Bool':
                    return new BooleanType();
                case 'callable':
                case 'callback':
                    return new CallableType();
                case 'resource':
                    return new ResourceType();
                case 'object':
                    return new ObjectType();
                case 'void':
                    return new VoidType();
                case 'array':
                    return new ArrayType();
                case 'iterable':
                    return new IterableType();
                default:
                    return new ClassType($reflector->reflectClass(current($phpDocTypes)));
            }
        }
        return $this->decorated->create($reflector, $type, $phpDocTypes, $nullable);
    }
}
