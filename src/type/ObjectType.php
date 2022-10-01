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

use function is_subclass_of;
use function strcasecmp;

final class ObjectType extends Type
{
    /**
     * Checks if $maybeParent is the same as $maybeChild, or a parent of $maybeChild.
     */
    public static function isSameOrParentClass(TypeName $maybeParent, TypeName $maybeChild): bool
    {
        if (0 === strcasecmp($maybeParent->qualifiedName(), $maybeChild->qualifiedName())) {
            return true;
        }

        if (is_subclass_of($maybeChild->qualifiedName(), $maybeParent->qualifiedName(), true)) {
            return true;
        }

        return false;
    }

    public function __construct(public readonly TypeName $className, bool $allowsNull)
    {
        parent::__construct($className->qualifiedName(), $allowsNull);
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if (!$other instanceof self) {
            return false;
        }

        return self::isSameOrParentClass($this->className, $other->className);
    }

    /**
     * @psalm-assert-if-true ObjectType $this
     */
    public function isObject(): bool
    {
        return true;
    }
}
