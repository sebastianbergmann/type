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
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

final class ReflectionMapper
{
    public function fromMethodReturnType(ReflectionMethod $method): Type
    {
        if (!$method->hasReturnType()) {
            return new UnknownType;
        }

        $returnType = $method->getReturnType();

        assert($returnType instanceof ReflectionNamedType || $returnType instanceof ReflectionUnionType);

        if ($returnType instanceof ReflectionNamedType) {
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

            if ($returnType->getName() === 'mixed') {
                return new MixedType;
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

        assert($returnType instanceof ReflectionUnionType);

        $types = [];

        foreach ($returnType->getTypes() as $type) {
            if ($type->getName() === 'self') {
                $types[] = ObjectType::fromName(
                    $method->getDeclaringClass()->getName(),
                    false
                );
            } else {
                $types[] = Type::fromName($type->getName(), false);
            }
        }

        return new UnionType(...$types);
    }
}
