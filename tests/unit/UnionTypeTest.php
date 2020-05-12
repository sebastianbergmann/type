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

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Type\UnionType
 *
 * @uses \SebastianBergmann\Type\Type
 * @uses \SebastianBergmann\Type\SimpleType
 * @uses \SebastianBergmann\Type\NullType
 */
final class UnionTypeTest extends TestCase
{
    public function testCanBeRepresentedAsString(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('int', false)
        );

        $this->assertSame('bool|int', $type->name());
    }

    public function testCanBeRepresentedAsStringForReturnTypeDeclaration(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('int', false)
        );

        $this->assertSame(': bool|int', $type->getReturnTypeDeclaration());
    }

    public function testCanBeRepresentedAsStringForNullableReturnTypeDeclaration(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('int', false),
            Type::fromName('null', true)
        );

        $this->assertSame(': bool|int|null', $type->getReturnTypeDeclaration());
    }

    public function testMayAllowNull(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('null', true)
        );

        $this->assertTrue($type->allowsNull());
    }

    public function testMayNotAllowNull(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('int', false)
        );

        $this->assertFalse($type->allowsNull());
    }

    /**
     * @dataProvider assignableProvider
     */
    public function testFoo(bool $expected, Type $type, UnionType $union): void
    {
        $this->assertSame($expected, $union->isAssignable($type));
    }

    public function assignableProvider(): array
    {
        return [
            [
                true,
                Type::fromName('bool', false),
                new UnionType(
                    Type::fromName('bool', false),
                    Type::fromName('int', false),
                ),
            ],
            [
                true,
                Type::fromName('int', false),
                new UnionType(
                    Type::fromName('bool', false),
                    Type::fromName('int', false),
                ),
            ],
            [
                false,
                Type::fromName('string', false),
                new UnionType(
                    Type::fromName('bool', false),
                    Type::fromName('int', false),
                ),
            ],
        ];
    }

    public function testCannotBeCreatedFromLessThanTwoTypes(): void
    {
        $this->expectException(RuntimeException::class);

        new UnionType;
    }

    public function testCannotBeCreatedFromUnknownType(): void
    {
        $this->expectException(RuntimeException::class);

        new UnionType(
            Type::fromName('int', false),
            Type::fromName('unknown type', false)
        );
    }

    public function testCannotBeCreatedFromVoidType(): void
    {
        $this->expectException(RuntimeException::class);

        new UnionType(
            Type::fromName('int', false),
            Type::fromName('void', false)
        );
    }
}
