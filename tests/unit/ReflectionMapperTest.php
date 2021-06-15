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
use ReflectionMethod;
use SebastianBergmann\Type\TestFixture\ChildClass;
use SebastianBergmann\Type\TestFixture\ChildClassWithoutParentClass;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareReturnTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareUnionReturnTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatHaveStaticReturnTypes;
use SebastianBergmann\Type\TestFixture\ParentClass;

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
 * @uses \SebastianBergmann\Type\UnionType
 * @uses \SebastianBergmann\Type\MixedType
 * @uses \SebastianBergmann\Type\StaticType
 * @uses \SebastianBergmann\Type\FalseType
 */
final class ReflectionMapperTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testMapsFromMethodReturnType(string $expected, ReflectionMethod $method): void
    {
        $this->assertSame($expected, (new ReflectionMapper)->fromMethodReturnType($method)->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMethodUnionReturnType(): void
    {
        $type = (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsBoolOrInt'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('bool|int', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMethodUnionReturnTypeWithSelf(): void
    {
        $type = (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsSelfOrStdClass'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame(ClassWithMethodsThatDeclareUnionReturnTypes::class . '|stdClass', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMethodMixedReturnType(): void
    {
        $type = (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsMixed'));

        $this->assertInstanceOf(MixedType::class, $type);
        $this->assertSame('mixed', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMethodStaticReturnType(): void
    {
        $type = (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ClassWithMethodsThatHaveStaticReturnTypes::class, 'returnsStatic'));

        $this->assertInstanceOf(StaticType::class, $type);
        $this->assertSame('static', $type->asString());
        $this->assertFalse($type->allowsNull());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMethodNullableStaticReturnType(): void
    {
        $type = (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ClassWithMethodsThatHaveStaticReturnTypes::class, 'returnsNullableStatic'));

        $this->assertInstanceOf(StaticType::class, $type);
        $this->assertSame('?static', $type->asString());
        $this->assertTrue($type->allowsNull());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMethodUnionWithStaticReturnType(): void
    {
        $type = (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ClassWithMethodsThatHaveStaticReturnTypes::class, 'returnsUnionWithStatic'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('static|stdClass', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMethodUnionReturnTypeWithIntOrFalse(): void
    {
        $type = (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsIntOrFalse'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('false|int', $type->name());
    }

    /**
     * @requires PHP < 7.4
     */
    public function testCannotMapFromMethodReturnTypeWhenParentIsUsedButNoParentClassExists(): void
    {
        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        (new ReflectionMapper)->fromMethodReturnType(new ReflectionMethod(ChildClassWithoutParentClass::class, 'method'));
    }

    public function typeProvider(): array
    {
        return [
            [
                'unknown type', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'noReturnType'),
            ],
            [
                'void', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'voidReturnType'),
            ],
            [
                ClassWithMethodsThatDeclareReturnTypes::class, new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'selfReturnType'),
            ],
            [
                ParentClass::class, new ReflectionMethod(ChildClass::class, 'bar'),
            ],
            [
                'stdClass', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'classReturnType'),
            ],
            [
                'object', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'objectReturnType'),
            ],
            [
                'array', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'arrayReturnType'),
            ],
            [
                'bool', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'boolReturnType'),
            ],
            [
                'float', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'floatReturnType'),
            ],
            [
                'int', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'intReturnType'),
            ],
            [
                'string', new ReflectionMethod(ClassWithMethodsThatDeclareReturnTypes::class, 'stringReturnType'),
            ],
        ];
    }
}
