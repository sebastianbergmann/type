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
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class ReflectionMapper
{
    public function fromReturnType(ReflectionFunction|ReflectionMethod $functionOrMethod): Type
    {
        if (!$this->hasReturnType($functionOrMethod)) {
            return new UnknownType;
        }

        $returnType = $this->returnType($functionOrMethod);

        if ($returnType instanceof ReflectionNamedType) {
            if ($functionOrMethod instanceof ReflectionFunction) {
                return Type::fromName(
                    $returnType->getName(),
                    $returnType->allowsNull()
                );
            }

            return $this->namedReturnTypeForMethod($returnType, $functionOrMethod);
        }

        /* @infection-ignore-all */
        assert($returnType instanceof ReflectionIntersectionType || $returnType instanceof ReflectionUnionType);

        if ($functionOrMethod instanceof ReflectionFunction) {
            return $this->intersectionOrUnionReturnTypeForFunction($returnType, $functionOrMethod);
        }

        return $this->intersectionOrUnionReturnTypeForMethod($returnType, $functionOrMethod);
    }

    private function hasReturnType(ReflectionFunction|ReflectionMethod $functionOrMethod): bool
    {
        if ($functionOrMethod->hasReturnType()) {
            return true;
        }

        return $functionOrMethod->hasTentativeReturnType();
    }

    private function returnType(ReflectionFunction|ReflectionMethod $functionOrMethod): ?ReflectionType
    {
        if ($functionOrMethod->hasReturnType()) {
            return $functionOrMethod->getReturnType();
        }

        return $functionOrMethod->getTentativeReturnType();
    }

    private function namedReturnTypeForMethod(ReflectionNamedType $returnType, ReflectionMethod $method): Type
    {
        if ($returnType->getName() === 'self') {
            return ObjectType::fromName(
                $method->getDeclaringClass()->getName(),
                $returnType->allowsNull()
            );
        }

        if ($returnType->getName() === 'static') {
            return new StaticType(
                TypeName::fromReflection($method->getDeclaringClass()),
                $returnType->allowsNull()
            );
        }

        if ($returnType->getName() === 'parent') {
            return ObjectType::fromName(
                $method->getDeclaringClass()->getParentClass()->getName(),
                $returnType->allowsNull()
            );
        }

        return Type::fromName(
            $returnType->getName(),
            $returnType->allowsNull()
        );
    }

    private function intersectionOrUnionReturnTypeForFunction(ReflectionIntersectionType|ReflectionUnionType $returnType, ReflectionFunction $function): IntersectionType|UnionType
    {
        $types = [];

        foreach ($returnType->getTypes() as $type) {
            $types[] = Type::fromName($type->getName(), $type->allowsNull());
        }

        if ($returnType instanceof ReflectionUnionType) {
            return new UnionType(...$types);
        }

        return new IntersectionType(...$types);
    }

    private function intersectionOrUnionReturnTypeForMethod(ReflectionIntersectionType|ReflectionUnionType $returnType, ReflectionMethod $method): IntersectionType|UnionType
    {
        $types = [];

        foreach ($returnType->getTypes() as $type) {
            if ($type->getName() === 'self') {
                $types[] = ObjectType::fromName(
                    $method->getDeclaringClass()->getName(),
                    false
                );
            } else {
                /* @infection-ignore-all */
                assert($type instanceof ReflectionNamedType);

                $types[] = Type::fromName($type->getName(), $type->allowsNull());
            }
        }

        if ($returnType instanceof ReflectionUnionType) {
            return new UnionType(...$types);
        }

        return new IntersectionType(...$types);
    }
}
