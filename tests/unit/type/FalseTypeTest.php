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

#[CoversClass(FalseType::class)]
#[CoversClass(Type::class)]
#[UsesClass(SimpleType::class)]
#[Small]
final class FalseTypeTest extends TestCase
{
    public function testHasName(): void
    {
        $this->assertSame('false', (new FalseType)->name());
    }

    #[DataProvider('assignableTypes')]
    public function testIsAssignable(Type $assignableType): void
    {
        $type = new FalseType;

        $this->assertTrue($type->isAssignable($assignableType));
    }

    public function assignableTypes(): array
    {
        return [
            [new FalseType],
            [new SimpleType('bool', false, false)],
        ];
    }

    #[DataProvider('notAssignableTypes')]
    public function testIsNotAssignable(Type $assignableType): void
    {
        $type = new FalseType;

        $this->assertFalse($type->isAssignable($assignableType));
    }

    public function notAssignableTypes(): array
    {
        return [
            [new SimpleType('bool', false, true)],
            [new SimpleType('int', false)],
            [new SimpleType('int', true)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), false)],
            [new ObjectType(TypeName::fromQualifiedName(self::class), true)],
            [new UnknownType],
        ];
    }

    public function testNotAllowNull(): void
    {
        $type = new FalseType;

        $this->assertFalse($type->allowsNull());
    }
}
