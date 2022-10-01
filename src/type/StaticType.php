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

final class StaticType extends Type
{
    public function __construct(public readonly TypeName $className, bool $allowsNull)
    {
        parent::__construct('static', $allowsNull);
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if (!$other instanceof ObjectType) {
            return false;
        }

        return ObjectType::isSameOrParentClass($this->className, $other->className);
    }

    /**
     * @psalm-assert-if-true StaticType $this
     */
    public function isStatic(): bool
    {
        return true;
    }
}
