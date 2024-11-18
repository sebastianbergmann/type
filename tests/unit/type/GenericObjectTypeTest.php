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
use stdClass;

#[CoversClass(GenericObjectType::class)]
#[CoversClass(Type::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class GenericObjectTypeTest extends TestCase
{
    private GenericObjectType $type;

    protected function setUp(): void
    {
        $this->type = new GenericObjectType(false);
    }

    public function testHasName(): void
    {
        $this->assertSame('object', $this->type->name());
    }

    public function testMayDisallowNull(): void
    {
        $this->assertFalse($this->type->allowsNull());
    }

    public function testMayAllowNull(): void
    {
        $type = new GenericObjectType(true);

        $this->assertTrue($type->allowsNull());
    }

    public function testObjectCanBeAssignedToGenericObject(): void
    {
        $this->assertTrue(
            $this->type->isAssignable(
                new ObjectType(TypeName::fromQualifiedName(stdClass::class), false),
            ),
        );
    }

    public function testNullCanBeAssignedToNullableGenericObject(): void
    {
        $type = new GenericObjectType(true);

        $this->assertTrue(
            $type->isAssignable(
                new NullType,
            ),
        );
    }

    public function testNonObjectCannotBeAssignedToGenericObject(): void
    {
        $this->assertFalse(
            $this->type->isAssignable(
                new SimpleType('bool', false),
            ),
        );
    }

    public function testCanBeQueriedForType(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($this->type->isGenericObject());

        $this->assertFalse($this->type->isCallable());
        $this->assertFalse($this->type->isFalse());
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
