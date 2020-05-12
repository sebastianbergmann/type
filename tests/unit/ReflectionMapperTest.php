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
use SebastianBergmann\Type\TestFixture\ChildClassWithoutParentClass;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareReturnTypes;

/**
 * @covers \SebastianBergmann\Type\ReflectionMapper
 *
 * @uses \SebastianBergmann\Type\Type
 * @uses \SebastianBergmann\Type\TypeName
 * @uses \SebastianBergmann\Type\GenericObjectType
 * @uses \SebastianBergmann\Type\ObjectType
 * @uses \SebastianBergmann\Type\SimpleType
 * @uses \SebastianBergmann\Type\UnknownType
 * @uses \SebastianBergmann\Type\VoidType
 */
final class ReflectionMapperTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testMapsFromMethodReturnType(string $expected, \ReflectionMethod $method): void
    {
        $this->assertSame($expected, (new ReflectionMapper)->fromMethodReturnType($method)->getReturnTypeDeclaration());
    }

    /**
     * @requires PHP < 7.4
     */
    public function testCannotMapFromMethodReturnTypeWhenParentIsUsedButNoParentClassExists(): void
    {
        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        (new ReflectionMapper)->fromMethodReturnType(new \ReflectionMethod(ChildClassWithoutParentClass::class, 'method'));
    }

    public function typeProvider(): array
    {
        return [
            [
                '', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'noReturnType'),
            ],
            [
                ': void', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'voidReturnType'),
            ],
            [
                ': SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareReturnTypes', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'selfReturnType'),
            ],
            [
                ': SebastianBergmann\Type\TestFixture\ParentClass', new \ReflectionMethod(ChildClass::class, 'bar'),
            ],
            [
                ': stdClass', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'classReturnType'),
            ],
            [
                ': object', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'objectReturnType'),
            ],
            [
                ': array', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'arrayReturnType'),
            ],
            [
                ': bool', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'boolReturnType'),
            ],
            [
                ': float', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'floatReturnType'),
            ],
            [
                ': int', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'intReturnType'),
            ],
            [
                ': string', new \ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'stringReturnType'),
            ],
        ];
    }
}
