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

final class NeverType extends Type
{
    public function __construct()
    {
        parent::__construct('never', false);
    }

    public function isAssignable(Type $other): bool
    {
        return $other instanceof self;
    }

    /**
     * @psalm-assert-if-true NeverType $this
     */
    public function isNever(): bool
    {
        return true;
    }
}
