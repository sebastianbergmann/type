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
 *
 * @uses \SebastianBergmann\Type\Type
 * @uses \SebastianBergmann\Type\TypeName
 * @uses \SebastianBergmann\Type\ObjectType
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

    public function testCanBeRepresentedAsStringForReturnTypeDeclaration(): void
    {
        $type = new StaticType(TypeName::fromQualifiedName('vendor\project\foo'), false);

        $this->assertSame(': static', $type->getReturnTypeDeclaration());
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

    /**
     * @requires PHP >= 8.0
     */
    public function testNullIsAssignableToNullableType(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            true
        );

        $this->assertTrue($type->isAssignable(Type::fromValue(null, true)));
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testNullIsNotAssignableToNotNullableType(): void
    {
        $type = new StaticType(
            TypeName::fromQualifiedName(ParentClassWithMethodThatHasStaticReturnType::class),
            false
        );

        $this->assertFalse($type->isAssignable(Type::fromValue(null, true)));
    }

    /**
     * @requires PHP >= 8.0
     */
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

    /**
     * @requires PHP >= 8.0
     */
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
}
