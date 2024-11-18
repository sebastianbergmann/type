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
use PHPUnit\Framework\TestCase;

#[CoversClass(VoidType::class)]
#[CoversClass(Type::class)]
#[Small]
final class VoidTypeTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: Type}>
     */
    public static function assignableTypes(): array
    {
        return [
            [new VoidType],
        ];
    }

    /**
     * @return non-empty-list<array{0: Type}>
     */
    public static function notAssignableTypes(): array
    {
        return [
            [new SimpleType('int', false)],
            [new SimpleType('int', true)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), false)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), true)],
            [new UnknownType],
        ];
    }

    public function testHasName(): void
    {
        $this->assertSame('void', (new VoidType)->name());
    }

    #[DataProvider('assignableTypes')]
    public function testIsAssignable(Type $assignableType): void
    {
        $type = new VoidType;

        $this->assertTrue($type->isAssignable($assignableType));
    }

    #[DataProvider('notAssignableTypes')]
    public function testIsNotAssignable(Type $assignableType): void
    {
        $type = new VoidType;

        $this->assertFalse($type->isAssignable($assignableType));
    }

    public function testNotAllowNull(): void
    {
        $type = new VoidType;

        $this->assertFalse($type->allowsNull());
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new VoidType;

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($type->isVoid());

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
        $this->assertFalse($type->isSimple());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isStatic());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isTrue());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnion());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnknown());
    }
}
