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

final class ReflectionMapper
{
    public function fromMethodReturnType(\ReflectionMethod $method): Type
    {
        if (!$method->hasReturnType()) {
            return new UnknownType;
        }

        $returnType = $method->getReturnType();

        \assert($returnType instanceof \ReflectionType);

        if ($returnType instanceof \ReflectionNamedType) {
            if ($returnType->getName() === 'self') {
                return ObjectType::fromName(
                    $method->getDeclaringClass()->getName(),
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

        throw new RuntimeException('Union Types are not yet supported');
    }
}
