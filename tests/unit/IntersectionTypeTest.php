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
use SebastianBergmann\Type\TestFixture\ChildClass;
use SebastianBergmann\Type\TestFixture\ParentClass;

/**
 * @covers \SebastianBergmann\Type\IntersectionType
 *
 * @uses \SebastianBergmann\Type\NullType
 * @uses \SebastianBergmann\Type\SimpleType
 * @uses \SebastianBergmann\Type\Type
 */
final class IntersectionTypeTest extends TestCase
{
    public function testCanBeRepresentedAsString(): void
    {
        $type = new IntersectionType(
            Type::fromName(ParentClass::class, false),
            Type::fromName(ChildClass::class, false)
        );

        $this->assertSame(ChildClass::class . '&' . ParentClass::class, $type->name());
    }

    public function testCanBeRepresentedAsStringForReturnTypeDeclaration(): void
    {
        $type = new IntersectionType(
            Type::fromName(ParentClass::class, false),
            Type::fromName(ChildClass::class, false)
        );

        $this->assertSame(': ' . ChildClass::class . '&' . ParentClass::class, $type->getReturnTypeDeclaration());
    }

    public function testDoesNotAllowNull(): void
    {
        $type = new IntersectionType(
            Type::fromName(ParentClass::class, false),
            Type::fromName(ChildClass::class, false)
        );

        $this->assertFalse($type->allowsNull());
    }

    public function testCannotBeCreatedFromLessThanTwoTypes(): void
    {
        $this->expectException(RuntimeException::class);

        new IntersectionType;
    }

    public function testEnsureNoDuplicateTypes(): void
    {
        $this->expectException(RuntimeException::class);

        new IntersectionType(
            Type::fromName(ParentClass::class, false),
            Type::fromName(ParentClass::class, false)
        );
    }

    public function testCannotBeCreatedFromNonObjectType(): void
    {
        $this->expectException(RuntimeException::class);

        new IntersectionType(
            Type::fromName('int', false),
            Type::fromName('bool', false),
        );
    }
}
