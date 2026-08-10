<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type;

use function array_filter;
use function assert;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class ReflectionMapper
{
    /**
     * @return list<Parameter>
     */
    public function fromParameterTypes(ReflectionFunction|ReflectionMethod $reflector): array
    {
        $parameters = [];

        foreach ($reflector->getParameters() as $parameter) {
            $type = $parameter->getType();

            $parameters[] = new Parameter(
                $parameter->getName(),
                $type === null ? new UnknownType : $this->mapType($type, $reflector),
            );
        }

        return $parameters;
    }

    public function fromReturnType(ReflectionFunction|ReflectionMethod $reflector): Type
    {
        $returnType = $reflector->getReturnType() ?? $reflector->getTentativeReturnType();

        if ($returnType === null) {
            return new UnknownType;
        }

        return $this->mapType($returnType, $reflector);
    }

    public function fromPropertyType(ReflectionProperty $reflector): Type
    {
        $propertyType = $reflector->getType();

        if ($propertyType === null) {
            return new UnknownType;
        }

        return $this->mapType($propertyType, $reflector);
    }

    private function mapType(ReflectionType $type, ReflectionFunction|ReflectionMethod|ReflectionProperty $reflector): Type
    {
        if ($type instanceof ReflectionNamedType) {
            return $this->mapNamedType($type, $reflector);
        }

        if ($type instanceof ReflectionUnionType) {
            return $this->mapUnionType($type, $reflector);
        }

        assert($type instanceof ReflectionIntersectionType);

        return $this->mapIntersectionType($type, $reflector);
    }

    private function mapNamedType(ReflectionNamedType $type, ReflectionFunction|ReflectionMethod|ReflectionProperty $reflector): Type
    {
        $classScope = !$reflector instanceof ReflectionFunction;
        $typeName   = $type->getName();

        assert($typeName !== '');

        if ($typeName === 'mixed') {
            return new MixedType;
        }

        if ($classScope) {
            if ($typeName === 'self') {
                return new ObjectType(
                    TypeName::fromReflection($reflector->getDeclaringClass()),
                    $type->allowsNull(),
                );
            }

            if ($typeName === 'static') {
                return new StaticType(
                    TypeName::fromReflection($reflector->getDeclaringClass()),
                    $type->allowsNull(),
                );
            }

            if ($typeName === 'parent') {
                $parentClass = $reflector->getDeclaringClass()->getParentClass();

                assert($parentClass !== false);

                return new ObjectType(
                    TypeName::fromReflection($parentClass),
                    $type->allowsNull(),
                );
            }
        }

        return Type::fromName(
            $typeName,
            $type->allowsNull(),
        );
    }

    private function mapUnionType(ReflectionUnionType $type, ReflectionFunction|ReflectionMethod|ReflectionProperty $reflector): Type
    {
        $types             = [];
        $objectType        = false;
        $genericObjectType = false;

        foreach ($type->getTypes() as $_type) {
            if ($_type instanceof ReflectionNamedType) {
                $namedType = $this->mapNamedType($_type, $reflector);

                if ($namedType instanceof GenericObjectType) {
                    $genericObjectType = true;
                } elseif ($namedType instanceof ObjectType) {
                    $objectType = true;
                }

                $types[] = $namedType;

                continue;
            }

            $types[] = $this->mapIntersectionType($_type, $reflector);
        }

        if ($objectType && $genericObjectType) {
            $types = array_filter(
                $types,
                static fn (Type $type): bool => !$type instanceof ObjectType,
            );
        }

        return new UnionType(...$types);
    }

    private function mapIntersectionType(ReflectionIntersectionType $type, ReflectionFunction|ReflectionMethod|ReflectionProperty $reflector): Type
    {
        $types = [];

        foreach ($type->getTypes() as $_type) {
            assert($_type instanceof ReflectionNamedType);

            $types[] = $this->mapNamedType($_type, $reflector);
        }

        return new IntersectionType(...$types);
    }
}
