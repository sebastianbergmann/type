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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(SimpleType::class)]
#[CoversClass(Type::class)]
#[UsesClass(TrueType::class)]
#[UsesClass(FalseType::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(TypeName::class)]
#[UsesClass(UnknownType::class)]
#[UsesClass(VoidType::class)]
#[Small]
final class SimpleTypeTest extends TestCase
{
    /**
     * @return non-empty-array<non-empty-string, array{0: Type, 1: Type}>
     */
    public static function assignablePairs(): array
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
     * @return non-empty-array<non-empty-string, array{0: Type, 1: Type}>
     */
    public static function notAssignablePairs(): array
    {
        return [
            'null to not nullable' => [new SimpleType('int', false), new NullType],
            'int to boolean'       => [new SimpleType('boolean', false), new SimpleType('int', false)],
            'object'               => [new SimpleType('boolean', false), new ObjectType(TypeName::fromQualifiedName(stdClass::class), true)],
            'unknown type'         => [new SimpleType('boolean', false), new UnknownType],
            'void'                 => [new SimpleType('boolean', false), new VoidType],
        ];
    }

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

    #[DataProvider('assignablePairs')]
    public function testIsAssignable(Type $assignTo, Type $assignedType): void
    {
        $this->assertTrue($assignTo->isAssignable($assignedType));
    }

    #[DataProvider('notAssignablePairs')]
    public function testIsNotAssignable(Type $assignTo, Type $assignedType): void
    {
        $this->assertFalse($assignTo->isAssignable($assignedType));
    }

    public function testCanHaveValue(): void
    {
        $type = Type::fromValue('string', false);

        $this->assertInstanceOf(SimpleType::class, $type);
        $this->assertSame('string', $type->value());
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new SimpleType('bool', false);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($type->isSimple());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isCallable());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isFalse());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isGenericObject());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isIntersection());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isIterable());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isMixed());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isNever());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isNull());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isObject());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isStatic());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isTrue());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnion());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnknown());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isVoid());
    }

    public function testNormalizesName(): void
    {
        $type = new SimpleType('BOOLEAN', false);

        $this->assertSame('bool', $type->name());
    }
}
