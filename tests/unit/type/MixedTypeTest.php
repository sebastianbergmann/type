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
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MixedType::class)]
#[CoversClass(Type::class)]
#[UsesClass(CallableType::class)]
#[UsesClass(GenericObjectType::class)]
#[UsesClass(SimpleType::class)]
#[Small]
final class MixedTypeTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: non-empty-string}>
     */
    public static function assignableTypes(): array
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
     * @param non-empty-string $otherType
     */
    #[DataProvider('assignableTypes')]
    #[TestDox('$otherType can be assigned to mixed')]
    public function testOtherTypeCanBeAssigned(string $otherType): void
    {
        $type = new MixedType;

        $this->assertTrue($type->isAssignable(Type::fromName($otherType, false)));
    }

    public function testCanBeQueriedForType(): void
    {
        $type = new MixedType;

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue($type->isMixed());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isCallable());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isFalse());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isGenericObject());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isIntersection());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isIterable());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isNever());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isNull());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isObject());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isSimple());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isStatic());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isTrue());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnion());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isUnknown());

        /** @phpstan-ignore method.impossibleType */
        $this->assertFalse($type->isVoid());
    }
}
