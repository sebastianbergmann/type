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

use function strtolower;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\TestFixture\ChildClass;
use SebastianBergmann\Type\TestFixture\ParentClass;
use someNamespaceA\NamespacedClass;

#[CoversClass(ObjectType::class)]
#[CoversClass(Type::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class ObjectTypeTest extends TestCase
{
    private ObjectType $childClass;
    private ObjectType $parentClass;

    protected function setUp(): void
    {
        $this->childClass = new ObjectType(
            TypeName::fromQualifiedName(ChildClass::class),
            false,
        );

        $this->parentClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            false,
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
        $classFromNamespaceA = new ObjectType(
            /** @phpstan-ignore class.notFound */
            TypeName::fromQualifiedName(NamespacedClass::class),
            false,
        );

        $classFromNamespaceB = new ObjectType(
            /** @phpstan-ignore class.notFound */
            TypeName::fromQualifiedName(\someNamespaceB\NamespacedClass::class),
            false,
        );
        $this->assertFalse($classFromNamespaceA->isAssignable($classFromNamespaceB));
    }

    public function testClassIsAssignableToSelfCaseInsensitively(): void
    {
        $classLowercased = new ObjectType(
            TypeName::fromQualifiedName(strtolower(ParentClass::class)),
            false,
        );

        $this->assertTrue($this->parentClass->isAssignable($classLowercased));
    }

    public function testNullIsAssignableToNullableType(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            true,
        );
        $this->assertTrue($someClass->isAssignable(Type::fromValue(null, true)));
    }

    public function testNullIsNotAssignableToNotNullableType(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            false,
        );

        $this->assertFalse($someClass->isAssignable(Type::fromValue(null, true)));
    }

    public function testPreservesNullNotAllowed(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            false,
        );

        $this->assertFalse($someClass->allowsNull());
    }

    public function testPreservesNullAllowed(): void
    {
        $someClass = new ObjectType(
            TypeName::fromQualifiedName(ParentClass::class),
            true,
        );

        $this->assertTrue($someClass->allowsNull());
    }

    public function testHasClassName(): void
    {
        $this->assertSame('SebastianBergmann\Type\TestFixture\ParentClass', $this->parentClass->className()->qualifiedName());
    }

    public function testCanBeQueriedForType(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($this->childClass->isObject());

        $this->assertFalse($this->childClass->isCallable());
        $this->assertFalse($this->childClass->isFalse());
        $this->assertFalse($this->childClass->isGenericObject());
        $this->assertFalse($this->childClass->isIntersection());
        $this->assertFalse($this->childClass->isIterable());
        $this->assertFalse($this->childClass->isMixed());
        $this->assertFalse($this->childClass->isNever());
        $this->assertFalse($this->childClass->isNull());
        $this->assertFalse($this->childClass->isSimple());
        $this->assertFalse($this->childClass->isStatic());
        $this->assertFalse($this->childClass->isTrue());
        $this->assertFalse($this->childClass->isUnion());
        $this->assertFalse($this->childClass->isUnknown());
        $this->assertFalse($this->childClass->isVoid());
    }
}
