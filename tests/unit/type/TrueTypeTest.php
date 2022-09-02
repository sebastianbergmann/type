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
 * @covers \SebastianBergmann\Type\TrueType
 * @covers \SebastianBergmann\Type\Type
 *
 * @uses \SebastianBergmann\Type\SimpleType
 *
 * @small
 */
final class TrueTypeTest extends TestCase
{
    public function testHasName(): void
    {
        $this->assertSame('true', (new TrueType)->name());
    }

    /**
     * @dataProvider assignableTypes
     */
    public function testIsAssignable(Type $assignableType): void
    {
        $type = new TrueType;

        $this->assertTrue($type->isAssignable($assignableType));
    }

    public function assignableTypes(): array
    {
        return [
            [new TrueType],
            [new SimpleType('bool', false, true)],
        ];
    }

    /**
     * @dataProvider notAssignableTypes
     */
    public function testIsNotAssignable(Type $assignableType): void
    {
        $type = new TrueType;

        $this->assertFalse($type->isAssignable($assignableType));
    }

    public function notAssignableTypes(): array
    {
        return [
            [new SimpleType('bool', false, false)],
            [new SimpleType('int', false)],
            [new SimpleType('int', true)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), false)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), true)],
            [new UnknownType],
        ];
    }

    public function testDoesNotAllowNull(): void
    {
        $type = new TrueType;

        $this->assertFalse($type->allowsNull());
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new TrueType;

        $this->assertFalse($type->isCallable());
        $this->assertFalse($type->isFalse());
        $this->assertFalse($type->isGenericObject());
        $this->assertFalse($type->isIntersection());
        $this->assertFalse($type->isIterable());
        $this->assertFalse($type->isMixed());
        $this->assertFalse($type->isNever());
        $this->assertFalse($type->isNull());
        $this->assertFalse($type->isObject());
        $this->assertFalse($type->isSimple());
        $this->assertFalse($type->isStatic());
        $this->assertTrue($type->isTrue());
        $this->assertFalse($type->isUnion());
        $this->assertFalse($type->isUnknown());
        $this->assertFalse($type->isVoid());
    }
}
