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

use function assert;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class ReflectionMapper
{
    public function fromReturnType(ReflectionFunctionAbstract $functionOrMethod): Type
    {
        if (!$this->hasReturnType($functionOrMethod)) {
            return new UnknownType;
        }

        $returnType = $this->returnType($functionOrMethod);

        assert($returnType instanceof ReflectionNamedType || $returnType instanceof ReflectionUnionType || $returnType instanceof ReflectionIntersectionType);

        if ($returnType instanceof ReflectionNamedType) {
            return $this->mapNamedType($returnType, $functionOrMethod);
        }

        if ($returnType instanceof ReflectionUnionType) {
            return $this->mapUnionType($returnType, $functionOrMethod);
        }

        if ($returnType instanceof ReflectionIntersectionType) {
            return $this->mapIntersectionType($returnType, $functionOrMethod);
        }
    }

    private function mapNamedType(ReflectionNamedType $type, ReflectionFunctionAbstract $functionOrMethod): Type
    {
        if ($functionOrMethod instanceof ReflectionMethod && $type->getName() === 'self') {
            return ObjectType::fromName(
                $functionOrMethod->getDeclaringClass()->getName(),
                $type->allowsNull()
            );
        }

        if ($functionOrMethod instanceof ReflectionMethod && $type->getName() === 'static') {
            return new StaticType(
                TypeName::fromReflection($functionOrMethod->getDeclaringClass()),
                $type->allowsNull()
            );
        }

        if ($type->getName() === 'mixed') {
            return new MixedType;
        }

        if ($functionOrMethod instanceof ReflectionMethod && $type->getName() === 'parent') {
            return ObjectType::fromName(
                $functionOrMethod->getDeclaringClass()->getParentClass()->getName(),
                $type->allowsNull()
            );
        }

        return Type::fromName(
            $type->getName(),
            $type->allowsNull()
        );
    }

    private function mapUnionType(ReflectionUnionType $type, ReflectionFunctionAbstract $functionOrMethod): Type
    {
        if (!$this->unionContainsOnlyNamedTypes($type)) {
            return $this->mapDistributedNormalFormType($type, $functionOrMethod);
        }

        $types = [];

        foreach ($type->getTypes() as $_type) {
            assert($_type instanceof ReflectionNamedType);

            $types[] = $this->mapNamedType($_type, $functionOrMethod);
        }

        return new UnionType(...$types);
    }

    private function mapDistributedNormalFormType(ReflectionUnionType $type, ReflectionFunctionAbstract $functionOrMethod): Type
    {
        $types = [];

        foreach ($type->getTypes() as $_type) {
            assert($_type instanceof ReflectionNamedType || $_type instanceof ReflectionIntersectionType);

            if ($_type instanceof ReflectionNamedType) {
                $types[] = $this->mapNamedType($_type, $functionOrMethod);

                continue;
            }

            $types[] = $this->mapIntersectionType($_type, $functionOrMethod);
        }

        return new DisjunctiveNormalFormType(...$types);
    }

    private function mapIntersectionType(ReflectionIntersectionType $type, ReflectionFunctionAbstract $functionOrMethod): Type
    {
        $types = [];

        foreach ($type->getTypes() as $_type) {
            assert($_type instanceof ReflectionNamedType);

            $types[] = $this->mapNamedType($_type, $functionOrMethod);
        }

        return new IntersectionType(...$types);
    }

    private function hasReturnType(ReflectionFunctionAbstract $functionOrMethod): bool
    {
        if ($functionOrMethod->hasReturnType()) {
            return true;
        }

        if (!method_exists($functionOrMethod, 'hasTentativeReturnType')) {
            return false;
        }

        return $functionOrMethod->hasTentativeReturnType();
    }

    private function returnType(ReflectionFunctionAbstract $functionOrMethod): ?ReflectionType
    {
        if ($functionOrMethod->hasReturnType()) {
            return $functionOrMethod->getReturnType();
        }

        if (!method_exists($functionOrMethod, 'getTentativeReturnType')) {
            return null;
        }

        return $functionOrMethod->getTentativeReturnType();
    }

    private function unionContainsOnlyNamedTypes(ReflectionUnionType $type): bool
    {
        foreach ($type->getTypes() as $_type) {
            if (!$_type instanceof ReflectionNamedType) {
                return false;
            }
        }

        return true;
    }
}
