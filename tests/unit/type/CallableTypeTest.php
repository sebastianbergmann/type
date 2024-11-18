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

use Closure;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\TestFixture\ClassWithCallbackMethods;
use SebastianBergmann\Type\TestFixture\ClassWithInvokeMethod;

#[CoversClass(CallableType::class)]
#[CoversClass(Type::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class CallableTypeTest extends TestCase
{
    private CallableType $type;

    protected function setUp(): void
    {
        $this->type = new CallableType(false);
    }

    public function testHasName(): void
    {
        $this->assertSame('callable', $this->type->name());
    }

    public function testMayDisallowNull(): void
    {
        $this->assertFalse($this->type->allowsNull());
    }

    public function testMayAllowNull(): void
    {
        $type = new CallableType(true);

        $this->assertTrue($type->allowsNull());
    }

    public function testNullCanBeAssignedToNullableCallable(): void
    {
        $type = new CallableType(true);

        $this->assertTrue($type->isAssignable(new NullType));
    }

    public function testCallableCanBeAssignedToCallable(): void
    {
        $this->assertTrue($this->type->isAssignable(new CallableType(false)));
    }

    public function testClosureCanBeAssignedToCallable(): void
    {
        $this->assertTrue(
            $this->type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(Closure::class),
                    false,
                ),
            ),
        );
    }

    public function testInvokableCanBeAssignedToCallable(): void
    {
        $this->assertTrue(
            $this->type->isAssignable(
                new ObjectType(
                    TypeName::fromQualifiedName(ClassWithInvokeMethod::class),
                    false,
                ),
            ),
        );
    }

    public function testStringWithFunctionNameCanBeAssignedToCallable(): void
    {
        $this->assertTrue(
            $this->type->isAssignable(
                Type::fromValue('SebastianBergmann\Type\TestFixture\callback_function', false),
            ),
        );
    }

    public function testStringWithClassNameAndStaticMethodNameCanBeAssignedToCallable(): void
    {
        $this->assertTrue(
            $this->type->isAssignable(
                Type::fromValue(ClassWithCallbackMethods::class . '::staticCallback', false),
            ),
        );
    }

    public function testArrayWithClassNameAndStaticMethodNameCanBeAssignedToCallable(): void
    {
        $this->assertTrue(
            $this->type->isAssignable(
                Type::fromValue([ClassWithCallbackMethods::class, 'staticCallback'], false),
            ),
        );
    }

    public function testArrayWithClassNameAndInstanceMethodNameCanBeAssignedToCallable(): void
    {
        $this->assertTrue(
            $this->type->isAssignable(
                Type::fromValue([new ClassWithCallbackMethods, 'nonStaticCallback'], false),
            ),
        );
    }

    public function testSomethingThatIsNotCallableCannotBeAssignedToCallable(): void
    {
        $this->assertFalse(
            $this->type->isAssignable(
                Type::fromValue(null, false),
            ),
        );
    }

    public function testObjectWithoutInvokeMethodCannotBeAssignedToCallable(): void
    {
        $this->assertFalse(
            $this->type->isAssignable(
                Type::fromValue(
                    new class
                    {
                    },
                    false,
                ),
            ),
        );
    }

    public function testCanBeQueriedForType(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($this->type->isCallable());

        $this->assertFalse($this->type->isFalse());
        $this->assertFalse($this->type->isGenericObject());
        $this->assertFalse($this->type->isIntersection());
        $this->assertFalse($this->type->isIterable());
        $this->assertFalse($this->type->isMixed());
        $this->assertFalse($this->type->isNever());
        $this->assertFalse($this->type->isNull());
        $this->assertFalse($this->type->isObject());
        $this->assertFalse($this->type->isSimple());
        $this->assertFalse($this->type->isStatic());
        $this->assertFalse($this->type->isTrue());
        $this->assertFalse($this->type->isUnion());
        $this->assertFalse($this->type->isUnknown());
        $this->assertFalse($this->type->isVoid());
    }
}
