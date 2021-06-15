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

class ClassWithMethodsThatDeclareUnionReturnTypes
{
    public function returnsBoolOrInt(): bool|int
    {
    }

    public function returnsMixed(): mixed
    {
    }

    public function returnsSelfOrStdClass(): self|\stdClass
    {
    }

    public function returnsIntOrFalse(): int|false
    {
    }
}
