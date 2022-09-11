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
use SebastianBergmann\Type\TestFixture\ChildClassThatExtendsClassWithMethodThatHasStaticReturnType;
use SebastianBergmann\Type\TestFixture\ParentClassWithMethodThatHasStaticReturnType;
use stdClass;

/**
 * @covers \SebastianBergmann\Type\StaticType
 * @covers \SebastianBergmann\Type\Type
 *
 * @uses \SebastianBergmann\Type\ObjectType
 * @uses \SebastianBergmann\Type\TypeName
 */
final class StaticTypeTest extends TestCase
{
    public function testHasName(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName('vendor\project\foo'), false);

        $this->assertSame('static', $type->name());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName('vendor\project\foo'), false);

        $this->assertSame('static', $type->asString());
    }

    public function testMayDisallowNull(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName('vendor\project\foo'), false);

        $this->assertFalse($type->allowsNull());
        $this->assertSame('static', $type->asString());
    }

    public function testMayAllowNull(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName('vendor\project\foo'), true);

        $this->assertTrue($type->allowsNull());
        $this->assertSame('?static', $type->asString());
    }

    public function testNullIsAssignableToNullableType(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true
        );

        $this->assertTrue($type->isAssignable(Type::fromValue(null, true)));
    }

    public function testNullIsNotAssignableToNotNullableType(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            false
        );

        $this->assertFalse($type->isAssignable(Type::fromValue(null, true)));
    }

    public function testSameTypeIsAssignable(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true
        );

        $this->assertTrue(
            $type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(
                        ParentClassWithMethodThatHasStaticReturnType::class
                    ),
                    false
                )
            )
        );
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testSubTypeIsAssignable(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true
        );

        $this->assertTrue(
            $type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(
                        ChildClassThatExtendsClassWithMethodThatHasStaticReturnType::class
                    ),
                    false
                )
            )
        );
    }

    public function testOtherTypeIsNotAssignable(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true
        );

        $this->assertFalse(
            $type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(
                        stdClass::class
                    ),
                    false
                )
            )
        );
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName(stdClass::class), false);

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
        $this->assertTrue($type->isStatic());
        $this->assertFalse($type->isTrue());
        $this->assertFalse($type->isUnion());
        $this->assertFalse($type->isUnknown());
        $this->assertFalse($type->isVoid());
    }
}
