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
                $parentClass = $method->getDeclaringClass()->getParentClass();

                if ($parentClass === false) {
                    throw new RuntimeException(
                        \sprintf(
                            '%s::%s() has a "parent" return type declaration but %s does not have a parent class',
                            $method->getDeclaringClass()->getName(),
                            $method->getName(),
                            $method->getDeclaringClass()->getName()
                        )
                    );
                }

                return ObjectType::fromName(
                    $parentClass->getName(),
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
