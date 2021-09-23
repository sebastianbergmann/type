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

use function class_exists;
use function strtolower;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\TestFixture\ChildClass;
use SebastianBergmann\Type\TestFixture\ParentClass;

/**
 * @covers \SebastianBergmann\Type\ObjectType
 * @covers \SebastianBergmann\Type\Type
 *
 * @uses \SebastianBergmann\Type\SimpleType
 * @uses \SebastianBergmann\Type\TypeName
 */
final class ObjectTypeTest extends TestCase
{
    private ObjectType $childClass;

    private ObjectType $parentClass;

    protected function setUp(): void
    {
        $this->childClass = new ObjectType(
            TypeName::fromQualifiedName(ChildClass::class),
            false
        );

        $this->parentClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            false
        );
    }

    public function testHasName(): void
    {
        $this->assertSame(ChildClass::class, $this->childClass->name());
    }

    public function testParentIsNotAssignableToChild(): void
    {
        $this->assertFalse($this->childClass->isAssignable($this->parentClass));
    }

    public function testChildIsAssignableToParent(): void
    {
        $this->assertTrue($this->parentClass->isAssignable($this->childClass));
    }

    public function testClassIsAssignableToSelf(): void
    {
        $this->assertTrue($this->parentClass->isAssignable($this->parentClass));
    }

    public function testSimpleTypeIsNotAssignableToClass(): void
    {
        $this->assertFalse($this->parentClass->isAssignable(new SimpleType('int', false)));
    }

    public function testClassFromOneNamespaceIsNotAssignableToClassInOtherNamespace(): void
    {
        class_exists(\someNamespaceA\NamespacedClass::class, true);
        class_exists(\someNamespaceB\NamespacedClass::class, true);
        $classFromNamespaceA = new ObjectType(
            TypeName::fromQualifiedName(\someNamespaceA\NamespacedClass::class),
            false
        );

        $classFromNamespaceB = new ObjectType(
            TypeName::fromQualifiedName(\someNamespaceB\NamespacedClass::class),
            false
        );
        $this->assertFalse($classFromNamespaceA->isAssignable($classFromNamespaceB));
    }

    public function testClassIsAssignableToSelfCaseInsensitively(): void
    {
        $classLowercased = new ObjectType(
            TypeName::fromQualifiedName(strtolower(ParentClass::class)),
            false
        );

        $this->assertTrue($this->parentClass->isAssignable($classLowercased));
    }

    public function testNullIsAssignableToNullableType(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            true
        );
        $this->assertTrue($someClass->isAssignable(Type::fromValue(null, true)));
    }

    public function testNullIsNotAssignableToNotNullableType(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            false
        );

        $this->assertFalse($someClass->isAssignable(Type::fromValue(null, true)));
    }

    public function testClassAliasIsAssignableToItsClass(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName('Parent_Class_Alias'),
            false
        );

        $this->assertTrue($someClass->isAssignable($this->parentClass));
    }

    public function testClassIsAssignableToItsAliasClass()
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName('Parent_Class_Alias'),
            false
        );

        $this->assertTrue($this->parentClass->isAssignable($someClass));
    }

    public function testPreservesNullNotAllowed(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            false
        );

        $this->assertFalse($someClass->allowsNull());
    }

    public function testPreservesNullAllowed(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            true
        );

        $this->assertTrue($someClass->allowsNull());
    }

    public function testHasClassName(): void
    {
        $this->assertSame('SebastianBergmann\Type\TestFixture\ParentClass', $this->parentClass->className()->qualifiedName());
    }

    public function testCanBeQueriedForType(): void
    {
        $this->assertFalse($this->childClass->isCallable());
        $this->assertFalse($this->childClass->isGenericObject());
        $this->assertFalse($this->childClass->isIntersection());
        $this->assertFalse($this->childClass->isIterable());
        $this->assertFalse($this->childClass->isMixed());
        $this->assertFalse($this->childClass->isNever());
        $this->assertFalse($this->childClass->isNull());
        $this->assertTrue($this->childClass->isObject());
        $this->assertFalse($this->childClass->isSimple());
        $this->assertFalse($this->childClass->isStatic());
        $this->assertFalse($this->childClass->isUnion());
        $this->assertFalse($this->childClass->isUnknown());
        $this->assertFalse($this->childClass->isVoid());
    }
}
