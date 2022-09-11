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
 * @covers \SebastianBergmann\Type\NeverType
 * @covers \SebastianBergmann\Type\Type
 */
final class NeverTypeTest extends TestCase
{
    public function testHasName(): void
    {
        $this->assertSame('never', (new NeverType)->name());
    }

    /**
     * @dataProvider assignableTypes
     */
    public function testIsAssignable(Type $assignableType): void
    {
        $type = new NeverType;

        $this->assertTrue($type->isAssignable($assignableType));
    }

    public function assignableTypes(): array
    {
        return [
            [new NeverType],
        ];
    }

    /**
     * @dataProvider notAssignableTypes
     */
    public function testIsNotAssignable(Type $assignableType): void
    {
        $type = new NeverType;

        $this->assertFalse($type->isAssignable($assignableType));
    }

    public function notAssignableTypes(): array
    {
        return [
            [new SimpleType('int', false)],
            [new SimpleType('int', true)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), false)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), true)],
            [new UnknownType],
        ];
    }

    public function testNotAllowNull(): void
    {
        $type = new NeverType;

        $this->assertFalse($type->allowsNull());
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new NeverType;

        $this->assertFalse($type->isCallable());
        $this->assertFalse($type->isFalse());
        $this->assertFalse($type->isGenericObject());
        $this->assertFalse($type->isIntersection());
        $this->assertFalse($type->isIterable());
        $this->assertFalse($type->isMixed());
        $this->assertTrue($type->isNever());
        $this->assertFalse($type->isNull());
        $this->assertFalse($type->isObject());
        $this->assertFalse($type->isSimple());
        $this->assertFalse($type->isStatic());
        $this->assertFalse($type->isTrue());
        $this->assertFalse($type->isUnion());
        $this->assertFalse($type->isUnknown());
        $this->assertFalse($type->isVoid());
    }
}
