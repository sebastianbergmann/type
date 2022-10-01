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
use function count;
use function implode;
use function in_array;
use function sort;

final class IntersectionType extends Type
{
    /**
     * @psalm-var non-empty-list<ObjectType>
     */
    private array $types;

    /**
     * @psalm-param non-empty-list<ObjectType> $types
     */
    public static function getFullName(Type ...$types): string
    {
        $names = [];

        foreach ($types as $type) {
            $names[] = $type->name;
        }

        sort($names);

        return implode('&', $names);
    }

    /**
     * @throws RuntimeException
     */
    public function __construct(Type ...$types)
    {
        $this->ensureMinimumOfTwoTypes(...$types);
        $this->ensureOnlyValidTypes(...$types);
        $this->ensureNoDuplicateTypes(...$types);

        parent::__construct(self::getFullName(...$types), false);

        $this->types = $types;
    }

    public function isAssignable(Type $other): bool
    {
        foreach ($this->types as $type) {
            if (!$type->isAssignable($other)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @psalm-assert-if-true IntersectionType $this
     */
    public function isIntersection(): bool
    {
        return true;
    }

    /**
     * @psalm-return non-empty-list<ObjectType>
     */
    public function types(): array
    {
        return $this->types;
    }

    /**
     * @throws RuntimeException
     */
    private function ensureMinimumOfTwoTypes(Type ...$types): void
    {
        if (count($types) < 2) {
            throw new RuntimeException(
                'An intersection type must be composed of at least two types'
            );
        }
    }

    /**
     * @throws RuntimeException
     */
    private function ensureOnlyValidTypes(Type ...$types): void
    {
        foreach ($types as $type) {
            if (!$type->isObject()) {
                throw new RuntimeException(
                    'An intersection type can only be composed of interfaces and classes'
                );
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    private function ensureNoDuplicateTypes(Type ...$types): void
    {
        $names = [];

        foreach ($types as $type) {
            assert($type instanceof ObjectType);

            $classQualifiedName = $type->className->qualifiedName();

            if (in_array($classQualifiedName, $names, true)) {
                throw new RuntimeException('An intersection type must not contain duplicate types');
            }

            $names[] = $classQualifiedName;
        }
    }
}
