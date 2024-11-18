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

#[CoversClass(TrueType::class)]
#[CoversClass(Type::class)]
#[UsesClass(SimpleType::class)]
#[Small]
final class TrueTypeTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: Type}>
     */
    public static function assignableTypes(): array
    {
        return [
            [new TrueType],
            [new SimpleType('bool', false, true)],
        ];
    }

    /**
     * @return non-empty-list<array{0: Type}>
     */
    public static function notAssignableTypes(): array
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

    public function testHasName(): void
    {
        $this->assertSame('true', (new TrueType)->name());
    }

    #[DataProvider('assignableTypes')]
    public function testIsAssignable(Type $assignableType): void
    {
        $type = new TrueType;

        $this->assertTrue($type->isAssignable($assignableType));
    }

    #[DataProvider('notAssignableTypes')]
    public function testIsNotAssignable(Type $assignableType): void
    {
        $type = new TrueType;

        $this->assertFalse($type->isAssignable($assignableType));
    }

    public function testDoesNotAllowNull(): void
    {
        $type = new TrueType;

        $this->assertFalse($type->allowsNull());
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new TrueType;

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($type->isTrue());

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
        $this->assertFalse($type->isUnion());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnknown());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isVoid());
    }
}
