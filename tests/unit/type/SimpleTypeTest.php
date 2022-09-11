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
use stdClass;

/**
 * @covers \SebastianBergmann\Type\SimpleType
 * @covers \SebastianBergmann\Type\Type
 *
 * @uses \SebastianBergmann\Type\FalseType
 * @uses \SebastianBergmann\Type\ObjectType
 * @uses \SebastianBergmann\Type\TrueType
 * @uses \SebastianBergmann\Type\Type
 * @uses \SebastianBergmann\Type\TypeName
 * @uses \SebastianBergmann\Type\UnknownType
 * @uses \SebastianBergmann\Type\VoidType
 */
final class SimpleTypeTest extends TestCase
{
    public function testCanBeBool(): void
    {
        $type = new SimpleType('bool', false);

        $this->assertSame('bool', $type->name());
    }

    public function testCanBeBoolean(): void
    {
        $type = new SimpleType('boolean', false);

        $this->assertSame('bool', $type->name());
    }

    public function testCanBeDouble(): void
    {
        $type = new SimpleType('double', false);

        $this->assertSame('float', $type->name());
    }

    public function testCanBeFloat(): void
    {
        $type = new SimpleType('float', false);

        $this->assertSame('float', $type->name());
    }

    public function testCanBeReal(): void
    {
        $type = new SimpleType('real', false);

        $this->assertSame('float', $type->name());
    }

    public function testCanBeInt(): void
    {
        $type = new SimpleType('int', false);

        $this->assertSame('int', $type->name());
    }

    public function testCanBeInteger(): void
    {
        $type = new SimpleType('integer', false);

        $this->assertSame('int', $type->name());
    }

    public function testCanBeArray(): void
    {
        $type = new SimpleType('array', false);

        $this->assertSame('array', $type->name());
    }

    public function testCanBeArray2(): void
    {
        $type = new SimpleType('[]', false);

        $this->assertSame('array', $type->name());
    }

    public function testMayAllowNull(): void
    {
        $type = new SimpleType('bool', true);

        $this->assertTrue($type->allowsNull());
    }

    public function testMayNotAllowNull(): void
    {
        $type = new SimpleType('bool', false);

        $this->assertFalse($type->allowsNull());
    }

    /**
     * @dataProvider assignablePairs
     */
    public function testIsAssignable(Type $assignTo, Type $assignedType): void
    {
        $this->assertTrue($assignTo->isAssignable($assignedType));
    }

    public function assignablePairs(): array
    {
        return [
            'nullable to not nullable'     => [new SimpleType('int', false), new SimpleType('int', true)],
            'not nullable to nullable'     => [new SimpleType('int', true), new SimpleType('int', false)],
            'nullable to nullable'         => [new SimpleType('int', true), new SimpleType('int', true)],
            'not nullable to not nullable' => [new SimpleType('int', false), new SimpleType('int', false)],
            'null to nullable'             => [new SimpleType('int', true), new NullType],
            'true to bool'                 => [new SimpleType('bool', false), new TrueType],
            'false to bool'                => [new SimpleType('bool', false), new FalseType],
        ];
    }

    /**
     * @dataProvider notAssignablePairs
     */
    public function testIsNotAssignable(Type $assignTo, Type $assignedType): void
    {
        $this->assertFalse($assignTo->isAssignable($assignedType));
    }

    public function notAssignablePairs(): array
    {
        return [
            'null to not nullable' => [new SimpleType('int', false), new NullType],
            'int to boolean'       => [new SimpleType('boolean', false), new SimpleType('int', false)],
            'object'               => [new SimpleType('boolean', false), new ObjectType(TypeName::fromQualifiedName(stdClass::class), true)],
            'unknown type'         => [new SimpleType('boolean', false), new UnknownType],
            'void'                 => [new SimpleType('boolean', false), new VoidType],
        ];
    }

    public function testCanHaveValue(): void
    {
        $this->assertSame('string', Type::fromValue('string', false)->value());
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new SimpleType('bool', false);

        $this->assertFalse($type->isCallable());
        $this->assertFalse($type->isFalse());
        $this->assertFalse($type->isGenericObject());
        $this->assertFalse($type->isIntersection());
        $this->assertFalse($type->isIterable());
        $this->assertFalse($type->isMixed());
        $this->assertFalse($type->isNever());
        $this->assertFalse($type->isNull());
        $this->assertFalse($type->isObject());
        $this->assertTrue($type->isSimple());
        $this->assertFalse($type->isStatic());
        $this->assertFalse($type->isTrue());
        $this->assertFalse($type->isUnion());
        $this->assertFalse($type->isUnknown());
        $this->assertFalse($type->isVoid());
    }

    public function testNormalizesName(): void
    {
        $type = new SimpleType('BOOLEAN', false);

        $this->assertSame('bool', $type->name());
    }
}
