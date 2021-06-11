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

use const PHP_MAJOR_VERSION;
use const PHP_MINOR_VERSION;
use function get_class;
use function gettype;
use function strtolower;

abstract class Type
{
    public static function fromValue(mixed $value, bool $allowsNull): self
    {
        $typeName = gettype($value);

        if ($typeName === 'object') {
            return new ObjectType(TypeName::fromQualifiedName(get_class($value)), $allowsNull);
        }

        $type = self::fromName($typeName, $allowsNull);

        if ($type instanceof SimpleType) {
            $type = new SimpleType($typeName, $allowsNull, $value);
        }

        return $type;
    }

    public static function fromName(string $typeName, bool $allowsNull): self
    {
        if ((PHP_MAJOR_VERSION > 8 || PHP_MINOR_VERSION >= 1) && strtolower($typeName) === 'never') {
            return new NeverType;
        }

        return match (strtolower($typeName)) {
            'callable'     => new CallableType($allowsNull),
            'iterable'     => new IterableType($allowsNull),
            'null'         => new NullType,
            'object'       => new GenericObjectType($allowsNull),
            'unknown type' => new UnknownType,
            'void'         => new VoidType,
            'array', 'bool', 'boolean', 'double', 'float', 'int', 'integer', 'real', 'resource', 'resource (closed)', 'string' => new SimpleType($typeName, $allowsNull),
            default => new ObjectType(TypeName::fromQualifiedName($typeName), $allowsNull),
        };
    }

    public function asString(): string
    {
        return ($this->allowsNull() ? '?' : '') . $this->name();
    }

    abstract public function isAssignable(self $other): bool;

    abstract public function name(): string;

    abstract public function allowsNull(): bool;
}
