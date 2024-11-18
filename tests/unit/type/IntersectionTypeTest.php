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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\TestFixture\AnInterface;
use SebastianBergmann\Type\TestFixture\AnotherInterface;
use SebastianBergmann\Type\TestFixture\ClassImplementingAnInterfaceAndAnotherInterface;

#[CoversClass(IntersectionType::class)]
#[CoversClass(Type::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class IntersectionTypeTest extends TestCase
{
    private IntersectionType $type;

    /**
     * @return non-empty-list<array{0: bool, 1: Type, 2: IntersectionType}>
     */
    public static function assignableTypes(): array
    {
        return [
            [
                true,
                Type::fromName(ClassImplementingAnInterfaceAndAnotherInterface::class, false),
                new IntersectionType(
                    Type::fromName(AnInterface::class, false),
                    Type::fromName(AnotherInterface::class, false),
                ),
            ],
            [
                false,
                Type::fromValue(false, false),
                new IntersectionType(
                    Type::fromName(AnInterface::class, false),
                    Type::fromName(AnotherInterface::class, false),
                ),
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->type = new IntersectionType(
            Type::fromName(AnInterface::class, false),
            Type::fromName(AnotherInterface::class, false),
        );
    }

    public function testTypesCanBeQueried(): void
    {
        $a = Type::fromName(AnInterface::class, false);
        $b = Type::fromName(AnotherInterface::class, false);

        $type = new IntersectionType($a, $b);

        $this->assertSame([$a, $b], $type->types());
    }

    public function testCanBeQueriedForType(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($this->type->isIntersection());

        $this->assertFalse($this->type->isCallable());
        $this->assertFalse($this->type->isFalse());
        $this->assertFalse($this->type->isGenericObject());
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
            AnInterface::class . '&' . AnotherInterface::class,
            $this->type->name(),
        );
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame(
            AnInterface::class . '&' . AnotherInterface::class,
            $this->type->asString(),
        );
    }

    public function testTypesOfIntersectionAreSortedByNameInStringRepresentation(): void
    {
        $type = new IntersectionType(
            Type::fromName(AnotherInterface::class, false),
            Type::fromName(AnInterface::class, false),
        );

        $this->assertSame(AnInterface::class . '&' . AnotherInterface::class, $type->asString());
    }

    #[DataProvider('assignableTypes')]
    public function testAssignableTypesAreRecognized(bool $expected, Type $type, IntersectionType $intersection): void
    {
        $this->assertSame($expected, $intersection->isAssignable($type));
    }

    public function testDoesNotAllowNull(): void
    {
        $this->assertFalse($this->type->allowsNull());
    }

    public function testCannotBeCreatedFromLessThanTwoTypes(): void
    {
        $this->expectException(RuntimeException::class);

        new IntersectionType;
    }

    public function testCanOnlyBeCreatedForInterfacesAndClasses(): void
    {
        $this->expectException(RuntimeException::class);

        new IntersectionType(
            Type::fromValue(false, false),
            Type::fromValue('string', false),
        );
    }

    public function testMustNotContainDuplicateTypes(): void
    {
        $this->expectException(RuntimeException::class);

        new IntersectionType(
            Type::fromName(AnInterface::class, false),
            Type::fromName(AnInterface::class, false),
            Type::fromName(AnotherInterface::class, false),
        );
    }
}
