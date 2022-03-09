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

final class Iterator implements \Iterator
{
    public function current(): mixed
    {
        return 'bar';
    }

    public function next(): void
    {
    }

    public function key(): mixed
    {
        return 'foo';
    }

    public function valid(): bool
    {
        return false;
    }

    public function rewind(): void
    {
    }
}
