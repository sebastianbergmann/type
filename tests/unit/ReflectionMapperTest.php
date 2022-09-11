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
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use SebastianBergmann\Type\TestFixture\AnInterface;
use SebastianBergmann\Type\TestFixture\AnotherInterface;
use SebastianBergmann\Type\TestFixture\ChildClass;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareDisjunctiveNormalFormParameterTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareDisjunctiveNormalFormReturnTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareIntersectionParameterTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareParameterTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareReturnTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareUnionParameterTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatDeclareUnionReturnTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodsThatHaveStaticReturnTypes;
use SebastianBergmann\Type\TestFixture\ClassWithMethodThatDeclaresFalseReturnType;
use SebastianBergmann\Type\TestFixture\ClassWithMethodThatDeclaresIntersectionReturnType;
use SebastianBergmann\Type\TestFixture\ClassWithMethodThatDeclaresNeverReturnType;
use SebastianBergmann\Type\TestFixture\ClassWithMethodThatDeclaresNullReturnType;
use SebastianBergmann\Type\TestFixture\ClassWithMethodThatDeclaresTrueReturnType;
use SebastianBergmann\Type\TestFixture\ParentClass;

/**
 * @covers \SebastianBergmann\Type\ReflectionMapper
 *
 * @uses \SebastianBergmann\Type\FalseType
 * @uses \SebastianBergmann\Type\GenericObjectType
 * @uses \SebastianBergmann\Type\IntersectionType
 * @uses \SebastianBergmann\Type\MixedType
 * @uses \SebastianBergmann\Type\NeverType
 * @uses \SebastianBergmann\Type\NullType
 * @uses \SebastianBergmann\Type\ObjectType
 * @uses \SebastianBergmann\Type\Parameter
 * @uses \SebastianBergmann\Type\SimpleType
 * @uses \SebastianBergmann\Type\StaticType
 * @uses \SebastianBergmann\Type\TrueType
 * @uses \SebastianBergmann\Type\Type
 * @uses \SebastianBergmann\Type\TypeName
 * @uses \SebastianBergmann\Type\UnionType
 * @uses \SebastianBergmann\Type\UnknownType
 * @uses \SebastianBergmann\Type\VoidType
 */
final class ReflectionMapperTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testMapsFromReturnType(string $expected, ReflectionFunctionAbstract $method): void
    {
        $this->assertSame($expected, (new ReflectionMapper)->fromReturnType($method)->name());
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testMapsFromIntersectionReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodThatDeclaresIntersectionReturnType::class, 'returnsAnInterfaceOrAnotherInterface'));

        $this->assertInstanceOf(IntersectionType::class, $type);
        $this->assertSame(AnInterface::class . '&' . AnotherInterface::class, $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromUnionReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsBoolOrInt'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('bool|int', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromUnionReturnTypeWithSelf(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsSelfOrStdClass'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame(ClassWithMethodsThatDeclareUnionReturnTypes::class . '|stdClass', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromMixedReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsMixed'));

        $this->assertInstanceOf(MixedType::class, $type);
        $this->assertSame('mixed', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromStaticReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatHaveStaticReturnTypes::class, 'returnsStatic'));

        $this->assertInstanceOf(StaticType::class, $type);
        $this->assertSame('static', $type->asString());
        $this->assertFalse($type->allowsNull());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromNullableStaticReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatHaveStaticReturnTypes::class, 'returnsNullableStatic'));

        $this->assertInstanceOf(StaticType::class, $type);
        $this->assertSame('?static', $type->asString());
        $this->assertTrue($type->allowsNull());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromUnionWithStaticReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatHaveStaticReturnTypes::class, 'returnsUnionWithStatic'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('static|stdClass', $type->name());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromUnionReturnTypeWithIntOrFalse(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareUnionReturnTypes::class, 'returnsIntOrFalse'));

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('false|int', $type->name());
    }

    /**
     * @requires PHP < 8.1
     */
    public function testMapsFromClassNamedNeverReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodThatDeclaresNeverReturnType::class, 'neverReturnType'));

        $this->assertInstanceOf(ObjectType::class, $type);
        $this->assertSame('SebastianBergmann\Type\TestFixture\never', $type->name());
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testMapsFromNeverReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodThatDeclaresNeverReturnType::class, 'neverReturnType'));

        $this->assertInstanceOf(NeverType::class, $type);
        $this->assertSame('never', $type->name());
    }

    /**
     * @requires PHP >= 8.2
     */
    public function testMapsFromTrueReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodThatDeclaresTrueReturnType::class, 'trueReturnType'));

        $this->assertInstanceOf(TrueType::class, $type);
        $this->assertSame('true', $type->name());
    }

    /**
     * @requires PHP >= 8.2
     */
    public function testMapsFromFalseReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodThatDeclaresFalseReturnType::class, 'falseReturnType'));

        $this->assertInstanceOf(FalseType::class, $type);
        $this->assertSame('false', $type->name());
    }

    /**
     * @requires PHP >= 8.2
     */
    public function testMapsFromNullReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodThatDeclaresNullReturnType::class, 'nullReturnType'));

        $this->assertInstanceOf(NullType::class, $type);
        $this->assertSame('null', $type->name());
    }

    /**
     * @requires PHP >= 8.2
     */
    public function testMapsFromDisjunctiveNormalFormReturnType(): void
    {
        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareDisjunctiveNormalFormReturnTypes::class, 'one'));
        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('(SebastianBergmann\Type\TestFixture\A&SebastianBergmann\Type\TestFixture\B)|SebastianBergmann\Type\TestFixture\D', $type->name());

        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareDisjunctiveNormalFormReturnTypes::class, 'two'));
        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('(SebastianBergmann\Type\TestFixture\D&SebastianBergmann\Type\TestFixture\X)|SebastianBergmann\Type\TestFixture\C|null', $type->name());

        $type = (new ReflectionMapper)->fromReturnType(new ReflectionMethod(ClassWithMethodsThatDeclareDisjunctiveNormalFormReturnTypes::class, 'three'));
        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame('(SebastianBergmann\Type\TestFixture\A&SebastianBergmann\Type\TestFixture\B&SebastianBergmann\Type\TestFixture\D)|int|null', $type->name());
    }

    public function testMapsFromParameters(): void
    {
        $method = new ReflectionMethod(ClassWithMethodsThatDeclareParameterTypes::class, 'unknown');
        $types  = (new ReflectionMapper)->fromParameterTypes($method);

        $this->assertCount(1, $types);
        $this->assertSame('x', $types[0]->name());
        $this->assertSame('', $types[0]->type()->asString());

        $method = new ReflectionMethod(ClassWithMethodsThatDeclareParameterTypes::class, 'named');
        $types  = (new ReflectionMapper)->fromParameterTypes($method);

        $this->assertCount(1, $types);
        $this->assertSame('x', $types[0]->name());
        $this->assertSame('SebastianBergmann\Type\TestFixture\A', $types[0]->type()->asString());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMapsFromUnionTypeParameters(): void
    {
        $method = new ReflectionMethod(ClassWithMethodsThatDeclareUnionParameterTypes::class, 'union');
        $types  = (new ReflectionMapper)->fromParameterTypes($method);

        $this->assertCount(1, $types);
        $this->assertSame('x', $types[0]->name());
        $this->assertSame('bool|int', $types[0]->type()->asString());
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testMapsFromIntersectionTypeParameters(): void
    {
        $method = new ReflectionMethod(ClassWithMethodsThatDeclareIntersectionParameterTypes::class, 'intersection');
        $types  = (new ReflectionMapper)->fromParameterTypes($method);

        $this->assertCount(1, $types);
        $this->assertSame('x', $types[0]->name());
        $this->assertSame('SebastianBergmann\Type\TestFixture\A&SebastianBergmann\Type\TestFixture\B', $types[0]->type()->asString());
    }

    /**
     * @requires PHP >= 8.2
     */
    public function testMapsFromDisjunctiveNormalFormParameters(): void
    {
        $method = new ReflectionMethod(ClassWithMethodsThatDeclareDisjunctiveNormalFormParameterTypes::class, 'dnfOne');
        $types  = (new ReflectionMapper)->fromParameterTypes($method);

        $this->assertCount(1, $types);
        $this->assertSame('x', $types[0]->name());
        $this->assertSame('(SebastianBergmann\Type\TestFixture\A&SebastianBergmann\Type\TestFixture\B)|SebastianBergmann\Type\TestFixture\D', $types[0]->type()->asString());

        $method = new ReflectionMethod(ClassWithMethodsThatDeclareDisjunctiveNormalFormParameterTypes::class, 'dnfTwo');
        $types  = (new ReflectionMapper)->fromParameterTypes($method);

        $this->assertCount(1, $types);
        $this->assertSame('x', $types[0]->name());
        $this->assertSame('(SebastianBergmann\Type\TestFixture\D&SebastianBergmann\Type\TestFixture\X)|SebastianBergmann\Type\TestFixture\C|null', $types[0]->type()->asString());

        $method = new ReflectionMethod(ClassWithMethodsThatDeclareDisjunctiveNormalFormParameterTypes::class, 'dnfThree');
        $types  = (new ReflectionMapper)->fromParameterTypes($method);

        $this->assertCount(1, $types);
        $this->assertSame('x', $types[0]->name());
        $this->assertSame('(SebastianBergmann\Type\TestFixture\A&SebastianBergmann\Type\TestFixture\B&SebastianBergmann\Type\TestFixture\D)|int|null', $types[0]->type()->asString());
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

            [
                'unknown type', new ReflectionFunction('SebastianBergmann\Type\TestFixture\noReturnType'),
            ],
            [
                'void', new ReflectionFunction('SebastianBergmann\Type\TestFixture\voidReturnType'),
            ],
            [
                'stdClass', new ReflectionFunction('SebastianBergmann\Type\TestFixture\classReturnType'),
            ],
            [
                'object', new ReflectionFunction('SebastianBergmann\Type\TestFixture\objectReturnType'),
            ],
            [
                'array', new ReflectionFunction('SebastianBergmann\Type\TestFixture\arrayReturnType'),
            ],
            [
                'bool', new ReflectionFunction('SebastianBergmann\Type\TestFixture\boolReturnType'),
            ],
            [
                'float', new ReflectionFunction('SebastianBergmann\Type\TestFixture\floatReturnType'),
            ],
            [
                'int', new ReflectionFunction('SebastianBergmann\Type\TestFixture\intReturnType'),
            ],
            [
                'string', new ReflectionFunction('SebastianBergmann\Type\TestFixture\stringReturnType'),
            ],
        ];
    }
}
