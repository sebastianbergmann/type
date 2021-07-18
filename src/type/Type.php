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

use ReflectionNamedType;
use const PHP_VERSION;
use function get_class;
use function gettype;
use function strtolower;
use function version_compare;

abstract class Type
{
    public static function fromValue(mixed $value, bool $allowsNull): self
    {
        if ($value === false) {
            return new FalseType;
        }

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

    public static function fromName(string $typeName, bool $allowsNull = false): self
    {
        if (version_compare(PHP_VERSION, '8.1.0-dev', '>=') && strtolower($typeName) === 'never') {
            return new NeverType;
        }

        $typeName = strtolower(trim($typeName));
        if (str_starts_with($typeName, '?')) {
            $allowsNull = true;
            $typeName   = substr($typeName, offset: 1);
        }

        $unionTypeNames = array_map('trim', explode('|', $typeName));
        if ($unionTypeNames !== [$typeName]) {
            return self::buildUnionType($unionTypeNames, $allowsNull);
        }

        return self::identifySingleTypeFromName($typeName, $allowsNull);
    }

    public static function fromReflection(ReflectionNamedType $type): self
    {
        return self::fromName($type->getName(), allowsNull: $type->allowsNull());
    }

    /**
     * @param array<string> $typeNames
     * @param bool $allowsNull
     *
     * @return UnionType
     */
    private static function buildUnionType(array $typeNames, bool $allowsNull): UnionType
    {
        $types = array_map(
            static fn(string $typeName) => self::identifySingleTypeFromName($typeName, allowsNull: false),
            $typeNames
        );
        if ($allowsNull) {
            $types[] = new NullType();
        }

        return new UnionType(...$types);
    }

    private static function identifySingleTypeFromName(string $typeName, bool $allowsNull): self
    {
        return match ($typeName) {
            'callable' => new CallableType($allowsNull),
            'false' => new FalseType(),
            'iterable' => new IterableType($allowsNull),
            'null' => new NullType(),
            'object' => new GenericObjectType($allowsNull),
            'unknown type' => new UnknownType(),
            'void' => new VoidType(),
            'array', 'bool', 'boolean', 'double', 'float', 'int', 'integer', 'real', 'resource', 'resource (closed)', 'string' => new SimpleType($typeName, $allowsNull),
            default => new ObjectType(TypeName::fromQualifiedName($typeName), $allowsNull),
        };
    }

    public function asString(): string
    {
        return ($this->allowsNull() ? '?' : '') . $this->name();
    }

    public function isCallable(): bool
    {
        return false;
    }

    public function isFalse(): bool
    {
        return false;
    }

    public function isGenericObject(): bool
    {
        return false;
    }

    public function isIterable(): bool
    {
        return false;
    }

    public function isMixed(): bool
    {
        return false;
    }

    public function isNever(): bool
    {
        return false;
    }

    public function isNull(): bool
    {
        return false;
    }

    public function isObject(): bool
    {
        return false;
    }

    public function isSimple(): bool
    {
        return false;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isUnion(): bool
    {
        return false;
    }

    public function isUnknown(): bool
    {
        return false;
    }

    public function isVoid(): bool
    {
        return false;
    }

    abstract public function isAssignable(self $other): bool;

    abstract public function name(): string;

    abstract public function allowsNull(): bool;
}
