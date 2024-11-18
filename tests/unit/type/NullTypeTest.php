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
use PHPUnit\Framework\TestCase;

#[CoversClass(NullType::class)]
#[CoversClass(Type::class)]
#[Small]
final class NullTypeTest extends TestCase
{
    private NullType $type;

    /**
     * @return non-empty-list<array{0: Type}>
     */
    public static function assignableTypes(): array
    {
        return [
            [new SimpleType('int', false)],
            [new SimpleType('int', true)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), false)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), true)],
            [new UnknownType],
        ];
    }

    /**
     * @return non-empty-array<non-empty-string, array{0: Type}>
     */
    public static function notAssignableTypes(): array
    {
        return [
            'void' => [new VoidType],
        ];
    }

    protected function setUp(): void
    {
        $this->type = new NullType;
    }

    #[DataProvider('assignableTypes')]
    public function testIsAssignable(Type $assignableType): void
    {
        $this->assertTrue($this->type->isAssignable($assignableType));
    }

    #[DataProvider('notAssignableTypes')]
    public function testIsNotAssignable(Type $assignedType): void
    {
        $this->assertFalse($this->type->isAssignable($assignedType));
    }

    public function testAllowsNull(): void
    {
        $this->assertTrue($this->type->allowsNull());
    }

    public function testHasName(): void
    {
        $this->assertSame('null', $this->type->name());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('null', $this->type->asString());
    }

    public function testCanBeQueriedForType(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($this->type->isNull());

        $this->assertFalse($this->type->isCallable());
        $this->assertFalse($this->type->isFalse());
        $this->assertFalse($this->type->isGenericObject());
        $this->assertFalse($this->type->isIntersection());
        $this->assertFalse($this->type->isIterable());
        $this->assertFalse($this->type->isMixed());
        $this->assertFalse($this->type->isNever());
        $this->assertFalse($this->type->isObject());
        $this->assertFalse($this->type->isSimple());
        $this->assertFalse($this->type->isStatic());
        $this->assertFalse($this->type->isTrue());
        $this->assertFalse($this->type->isUnion());
        $this->assertFalse($this->type->isUnknown());
        $this->assertFalse($this->type->isVoid());
    }
}
