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
 *
 * @uses \SebastianBergmann\Type\Type
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

    public function testCanBeRepresentedAsStringForReturnTypeDeclaration(): void
    {
        $type = new MixedType;

        $this->assertSame(': mixed', $type->getReturnTypeDeclaration());
    }

    public function testAllowsNull(): void
    {
        $type = new MixedType;

        $this->assertTrue($type->allowsNull());
    }

    /**
     * @dataProvider typeProvider
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
}
