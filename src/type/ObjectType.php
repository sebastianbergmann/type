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
    private TypeName $className;

    public function __construct(TypeName $className, bool $allowsNull)
    {
        parent::__construct($allowsNull);

        $this->className  = $className;
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($other instanceof self) {
            if (0 === strcasecmp($this->className->qualifiedName(), $other->className->qualifiedName())) {
                return true;
            }

            if (is_subclass_of($other->className->qualifiedName(), $this->className->qualifiedName(), true)) {
                return true;
            }
        }

        return false;
    }

    public function name(): string
    {
        return $this->className->qualifiedName();
    }

    public function className(): TypeName
    {
        return $this->className;
    }

    /**
     * @psalm-assert-if-true ObjectType $this
     */
    public function isObject(): bool
    {
        return true;
    }
}
