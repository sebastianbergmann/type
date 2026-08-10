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

use function class_exists;
use function count;
use function explode;
use function function_exists;
use function is_array;
use function is_object;
use function is_string;
use function method_exists;
use function str_contains;
use Closure;
use ReflectionMethod;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class CallableType extends Type
{
    private bool $allowsNull;

    public function __construct(bool $nullable)
    {
        $this->allowsNull = $nullable;
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($other instanceof self) {
            return true;
        }

        if ($other instanceof ObjectType) {
            if ($this->isClosure($other)) {
                return true;
            }

            if ($this->hasInvokeMethod($other)) {
                return true;
            }
        }

        if ($other instanceof SimpleType) {
            if ($this->isFunction($other)) {
                return true;
            }

            if ($this->isClassCallback($other)) {
                return true;
            }

            if ($this->isObjectCallback($other)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return 'callable'
     */
    public function name(): string
    {
        return 'callable';
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function isCallable(): bool
    {
        return true;
    }

    private function isClosure(ObjectType $type): bool
    {
        return $type->className()->qualifiedName() === Closure::class;
    }

    private function hasInvokeMethod(ObjectType $type): bool
    {
        return method_exists($type->className()->qualifiedName(), '__invoke');
    }

    private function isFunction(SimpleType $type): bool
    {
        $value = $type->value();

        if (!is_string($value)) {
            return false;
        }

        return function_exists($value);
    }

    private function isObjectCallback(SimpleType $type): bool
    {
        $value = $type->value();

        if (!is_array($value)) {
            return false;
        }

        if (count($value) !== 2) {
            return false;
        }

        if (!isset($value[0], $value[1])) {
            return false;
        }

        if (!is_object($value[0]) || !is_string($value[1])) {
            return false;
        }

        return method_exists($value[0], $value[1]);
    }

    private function isClassCallback(SimpleType $type): bool
    {
        $value = $type->value();

        if (is_string($value)) {
            if (!str_contains($value, '::')) {
                return false;
            }

            [$className, $methodName] = explode('::', $value);
        } elseif (is_array($value)) {
            if (count($value) !== 2) {
                return false;
            }

            if (!isset($value[0], $value[1])) {
                return false;
            }

            if (!is_string($value[0]) || !is_string($value[1])) {
                return false;
            }

            [$className, $methodName] = $value;
        } else {
            return false;
        }

        if (!class_exists($className)) {
            return false;
        }

        if (!method_exists($className, $methodName)) {
            return false;
        }

        $method = new ReflectionMethod($className, $methodName);

        return $method->isPublic() && $method->isStatic();
    }
}
