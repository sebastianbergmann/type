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
 * @covers \SebastianBergmann\Type\Type
 * @covers \SebastianBergmann\Type\UnknownType
 */
final class UnknownTypeTest extends TestCase
{
    /**
     * @var UnknownType
     */
    private $type;

    protected function setUp(): void
    {
        $this->type = new UnknownType;
    }

    /**
     * @dataProvider assignableTypes
     */
    public function testIsAssignable(Type $assignableType): void
    {
        $this->assertTrue($this->type->isAssignable($assignableType));
    }

    public function assignableTypes(): array
    {
        return [
            [new SimpleType('int', false)],
            [new SimpleType('int', true)],
            [new VoidType],
            [new ObjectType(TypeName::fromQualifiedName(self::class), false)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), true)],
            [new UnknownType],
        ];
    }

    public function testAllowsNull(): void
    {
        $this->assertTrue($this->type->allowsNull());
    }

    public function testHasName(): void
    {
        $this->assertSame('unknown type', $this->type->name());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('', $this->type->asString());
    }

    public function testCanBeQueriedForType(): void
    {
        $this->assertFalse($this->type->isCallable());
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
        $this->assertTrue($this->type->isUnknown());
        $this->assertFalse($this->type->isVoid());
    }
}
