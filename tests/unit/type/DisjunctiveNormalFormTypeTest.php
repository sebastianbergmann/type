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
use SebastianBergmann\Type\TestFixture\AnInterface;
use SebastianBergmann\Type\TestFixture\AnotherInterface;
use SebastianBergmann\Type\TestFixture\ClassImplementingAnInterfaceAndAnotherInterface;

/**
 * @covers \SebastianBergmann\Type\DisjunctiveNormalFormType
 * @covers \SebastianBergmann\Type\Type
 *
 * @uses \SebastianBergmann\Type\FalseType
 * @uses \SebastianBergmann\Type\IntersectionType
 * @uses \SebastianBergmann\Type\ObjectType
 * @uses \SebastianBergmann\Type\SimpleType
 * @uses \SebastianBergmann\Type\TypeName
 *
 * @requires PHP >= 8.2
 */
final class DisjunctiveNormalFormTypeTest extends TestCase
{
    /**
     * @psalm-var DisjunctiveNormalFormType
     */
    private $type;

    protected function setUp(): void
    {
        $this->type = new DisjunctiveNormalFormType(
            new IntersectionType(
                Type::fromName(AnInterface::class, false),
                Type::fromName(AnotherInterface::class, false)
            ),
            Type::fromName('bool', false)
        );
    }

    public function testCanBeQueriedForType(): void
    {
        $this->assertFalse($this->type->isCallable());
        $this->assertTrue($this->type->isDisjunctiveNormalForm());
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

    public function testHasName(): void
    {
        $this->assertSame(
            '(' . AnInterface::class . '&' . AnotherInterface::class . ')|bool',
            $this->type->name()
        );
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame(
            '(' . AnInterface::class . '&' . AnotherInterface::class . ')|bool',
            $this->type->asString()
        );
    }

    /**
     * @dataProvider assignableProvider
     */
    public function testAssignableTypesAreRecognized(bool $expected, Type $type, DisjunctiveNormalFormType $disjunctiveNormalFormType): void
    {
        $this->assertSame($expected, $disjunctiveNormalFormType->isAssignable($type));
    }

    public function assignableProvider(): array
    {
        return [
            [
                true,
                Type::fromName(ClassImplementingAnInterfaceAndAnotherInterface::class, false),
                new DisjunctiveNormalFormType(
                    new IntersectionType(
                        Type::fromName(AnInterface::class, false),
                        Type::fromName(AnotherInterface::class, false)
                    ),
                    Type::fromName('bool', false)
                ),
            ],
            [
                true,
                Type::fromValue(false, false),
                new DisjunctiveNormalFormType(
                    new IntersectionType(
                        Type::fromName(AnInterface::class, false),
                        Type::fromName(AnotherInterface::class, false)
                    ),
                    Type::fromName('bool', false)
                ),
            ],
            [
                false,
                Type::fromValue(1, false),
                new DisjunctiveNormalFormType(
                    new IntersectionType(
                        Type::fromName(AnInterface::class, false),
                        Type::fromName(AnotherInterface::class, false)
                    ),
                    Type::fromName('bool', false)
                ),
            ],
        ];
    }

    public function testMayAllowNull(): void
    {
        $type = new DisjunctiveNormalFormType(
            new IntersectionType(
                Type::fromName(AnInterface::class, false),
                Type::fromName(AnotherInterface::class, false)
            ),
            Type::fromName('null', true)
        );

        $this->assertTrue($type->allowsNull());
    }

    public function testMayNotAllowNull(): void
    {
        $type = new DisjunctiveNormalFormType(
            new IntersectionType(
                Type::fromName(AnInterface::class, false),
                Type::fromName(AnotherInterface::class, false)
            ),
            Type::fromName('bool', false)
        );

        $this->assertFalse($type->allowsNull());
    }

    public function testCannotBeCreatedFromLessThanTwoTypes(): void
    {
        $this->expectException(RuntimeException::class);

        new DisjunctiveNormalFormType;
    }

    public function testCannotBeCreatedFromUnknownType(): void
    {
        $this->expectException(RuntimeException::class);

        new DisjunctiveNormalFormType(
            Type::fromName('int', false),
            Type::fromName('unknown type', false)
        );
    }

    public function testCannotBeCreatedFromVoidType(): void
    {
        $this->expectException(RuntimeException::class);

        new DisjunctiveNormalFormType(
            Type::fromName('int', false),
            Type::fromName('void', false)
        );
    }
}
