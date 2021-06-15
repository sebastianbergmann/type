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
 * @covers \SebastianBergmann\Type\FalseType
 *
 * @uses \SebastianBergmann\Type\Type
 * @uses \SebastianBergmann\Type\SimpleType
 */
final class FalseTypeTest extends TestCase
{
    public function testHasName(): void
    {
        $this->assertSame('false', (new FalseType)->name());
    }

    /**
     * @dataProvider assignableTypes
     */
    public function testIsAssignable(Type $assignableType): void
    {
        $type = new FalseType;

        $this->assertTrue($type->isAssignable($assignableType));
    }

    public function assignableTypes(): array
    {
        return [
            [new FalseType],
            [new SimpleType('bool', false, false)],
        ];
    }

    /**
     * @dataProvider notAssignableTypes
     */
    public function testIsNotAssignable(Type $assignableType): void
    {
        $type = new FalseType;

        $this->assertFalse($type->isAssignable($assignableType));
    }

    public function notAssignableTypes(): array
    {
        return [
            [new SimpleType('bool', false, true)],
            [new SimpleType('int', false)],
            [new SimpleType('int', true)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), false)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), true)],
            [new UnknownType],
        ];
    }

    public function testNotAllowNull(): void
    {
        $type = new FalseType;

        $this->assertFalse($type->allowsNull());
    }

    public function testCannotBeRepresentedAsStringForReturnTypeDeclaration(): void
    {
        $type = new FalseType;

        $this->expectException(LogicException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $type->getReturnTypeDeclaration();
    }
}
