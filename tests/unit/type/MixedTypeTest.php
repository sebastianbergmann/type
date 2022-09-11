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

/**
 * @covers \SebastianBergmann\Type\MixedType
 * @covers \SebastianBergmann\Type\Type
 *
 * @uses \SebastianBergmann\Type\CallableType
 * @uses \SebastianBergmann\Type\GenericObjectType
 * @uses \SebastianBergmann\Type\SimpleType
 */
final class MixedTypeTest extends TestCase
{
    public function testHasName(): void
    {
        $type = new MixedType;

        $this->assertSame('mixed', $type->name());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $type = new MixedType;

        $this->assertSame('mixed', $type->asString());
    }

    public function testAllowsNull(): void
    {
        $type = new MixedType;

        $this->assertTrue($type->allowsNull());
    }

    /**
     * @dataProvider typeProvider
     *
     * @testdox $otherType can be assigned to mixed
     */
    public function testOtherTypeCanBeAssigned(string $otherType): void
    {
        $type = new MixedType;

        $this->assertTrue($type->isAssignable(Type::fromName($otherType, false)));
    }

    public function typeProvider(): array
    {
        return [
            ['array'],
            ['bool'],
            ['callable'],
            ['int'],
            ['float'],
            ['null'],
            ['object'],
            ['resource'],
            ['string'],
        ];
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new MixedType;

        $this->assertFalse($type->isCallable());
        $this->assertFalse($type->isFalse());
        $this->assertFalse($type->isGenericObject());
        $this->assertFalse($type->isIntersection());
        $this->assertFalse($type->isIterable());
        $this->assertTrue($type->isMixed());
        $this->assertFalse($type->isNever());
        $this->assertFalse($type->isNull());
        $this->assertFalse($type->isObject());
        $this->assertFalse($type->isSimple());
        $this->assertFalse($type->isStatic());
        $this->assertFalse($type->isTrue());
        $this->assertFalse($type->isUnion());
        $this->assertFalse($type->isUnknown());
        $this->assertFalse($type->isVoid());
    }
}
