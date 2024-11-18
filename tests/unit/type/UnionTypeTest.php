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
use SebastianBergmann\Type\TestFixture\AnInterface;
use SebastianBergmann\Type\TestFixture\AnotherInterface;

#[CoversClass(UnionType::class)]
#[CoversClass(Type::class)]
#[UsesClass(NullType::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(IntersectionType::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class UnionTypeTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: string, 1: Type}>
     */
    public static function stringRepresentationProvider(): array
    {
        return [
            [
                'bool|int',
                new UnionType(
                    Type::fromName('bool', false),
                    Type::fromName('int', false),
                ),
            ],
            [
                '(' . AnInterface::class . '&' . AnotherInterface::class . ')|bool',
                new UnionType(
                    new IntersectionType(
                        Type::fromName(AnInterface::class, false),
                        Type::fromName(AnotherInterface::class, false),
                    ),
                    Type::fromName('bool', false),
                ),
            ],
        ];
    }

    /**
     * @return non-empty-list<array{0: bool, 1: Type, 2: UnionType}>
     */
    public static function assignableProvider(): array
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

    #[DataProvider('stringRepresentationProvider')]
    public function testCanBeRepresentedAsString(string $expected, Type $type): void
    {
        $this->assertSame($expected, $type->asString());
    }

    public function testTypesCanBeQueried(): void
    {
        $bool = Type::fromName('bool', false);
        $null = Type::fromName('null', true);
        $type = new UnionType($bool, $null);

        $this->assertSame([$bool, $null], $type->types());
    }

    public function testMayAllowNull(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('null', true),
        );

        $this->assertTrue($type->allowsNull());
    }

    public function testMayNotAllowNull(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('int', false),
        );

        $this->assertFalse($type->allowsNull());
    }

    public function testMayContainIntersectionType(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            new IntersectionType(
                Type::fromName(AnInterface::class, false),
                Type::fromName(AnotherInterface::class, false),
            ),
        );

        $this->assertTrue($type->containsIntersectionTypes());
    }

    public function testMayNotContainIntersectionType(): void
    {
        $type = new UnionType(
            Type::fromName('bool', false),
            Type::fromName('int', false),
        );

        $this->assertFalse($type->containsIntersectionTypes());
    }

    #[DataProvider('assignableProvider')]
    public function testAssignableTypesAreRecognized(bool $expected, Type $type, UnionType $union): void
    {
        $this->assertSame($expected, $union->isAssignable($type));
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
            Type::fromName('unknown type', false),
        );
    }

    public function testCannotBeCreatedFromVoidType(): void
    {
        $this->expectException(RuntimeException::class);

        new UnionType(
            Type::fromName('int', false),
            Type::fromName('void', false),
        );
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new UnionType(
            new SimpleType('bool', false),
            new SimpleType('string', false),
        );

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($type->isUnion());

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
        $this->assertFalse($type->isUnknown());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isVoid());
    }
}
