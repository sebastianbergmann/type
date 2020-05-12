<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type\TestFixture;

class ClassWithMethodsThatDeclareReturnTypes
{
    /* @noinspection ReturnTypeCanBeDeclaredInspection */
    public function noReturnType()
    {
    }

    public function voidReturnType(): void
    {
    }

    public function selfReturnType(): self
    {
    }

    public function classReturnType(): \stdClass
    {
    }

    public function objectReturnType(): object
    {
    }

    public function arrayReturnType(): array
    {
    }

    public function boolReturnType(): bool
    {
    }

    public function floatReturnType(): float
    {
    }

    public function intReturnType(): int
    {
    }

    public function stringReturnType(): string
    {
    }
}
