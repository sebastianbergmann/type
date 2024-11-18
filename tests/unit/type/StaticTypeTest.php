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
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\TestFixture\ChildClassThatExtendsClassWithMethodThatHasStaticReturnType;
use SebastianBergmann\Type\TestFixture\ParentClassWithMethodThatHasStaticReturnType;
use stdClass;

#[CoversClass(StaticType::class)]
#[CoversClass(Type::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class StaticTypeTest extends TestCase
{
    public function testHasName(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName(self::class), false);

        $this->assertSame('static', $type->name());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName(self::class), false);

        $this->assertSame('static', $type->asString());
    }

    public function testMayDisallowNull(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName(self::class), false);

        $this->assertFalse($type->allowsNull());
        $this->assertSame('static', $type->asString());
    }

    public function testMayAllowNull(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName(self::class), true);

        $this->assertTrue($type->allowsNull());
        $this->assertSame('?static', $type->asString());
    }

    public function testNullIsAssignableToNullableType(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true,
        );

        $this->assertTrue($type->isAssignable(Type::fromValue(null, true)));
    }

    public function testNullIsNotAssignableToNotNullableType(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            false,
        );

        $this->assertFalse($type->isAssignable(Type::fromValue(null, true)));
    }

    public function testSameTypeIsAssignable(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true,
        );

        $this->assertTrue(
            $type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(
                        ParentClassWithMethodThatHasStaticReturnType::class,
                    ),
                    false,
                ),
            ),
        );
    }

    public function testSubTypeIsAssignable(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true,
        );

        $this->assertTrue(
            $type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(
                        ChildClassThatExtendsClassWithMethodThatHasStaticReturnType::class,
                    ),
                    false,
                ),
            ),
        );
    }

    public function testOtherTypeIsNotAssignable(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true,
        );

        $this->assertFalse(
            $type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(
                        stdClass::class,
                    ),
                    false,
                ),
            ),
        );
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName(stdClass::class), false);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($type->isStatic());

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
        $this->assertFalse($type->isTrue());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnion());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnknown());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isVoid());
    }
}
