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

use function strtolower;

final class SimpleType extends Type
{
    public function __construct(string $name, bool $allowsNull, public readonly mixed $value = null)
    {
        parent::__construct($this->normalize($name), $allowsNull);
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($this->name === 'bool' && $other->name === 'true') {
            return true;
        }

        if ($this->name === 'bool' && $other->name === 'false') {
            return true;
        }

        if ($other instanceof self) {
            return $this->name === $other->name;
        }

        return false;
    }

    /**
     * @psalm-assert-if-true SimpleType $this
     */
    public function isSimple(): bool
    {
        return true;
    }

    private function normalize(string $name): string
    {
        $name = strtolower($name);

        return match ($name) {
            'boolean' => 'bool',
            'real', 'double' => 'float',
            'integer' => 'int',
            '[]'      => 'array',
            default   => $name,
        };
    }
}
