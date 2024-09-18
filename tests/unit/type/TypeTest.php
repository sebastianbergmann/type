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
use stdClass;

#[CoversClass(Type::class)]
#[UsesClass(CallableType::class)]
#[UsesClass(GenericObjectType::class)]
#[UsesClass(IterableType::class)]
#[UsesClass(NeverType::class)]
#[UsesClass(ObjectType::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(TypeName::class)]
#[Small]
final class TypeTest extends TestCase
{
    /**
     * @return non-empty-array<string, array{0: mixed, 1: bool, 2: Type}>
     */
    public static function valuesToNullableType(): array
    {
        return [
            '?null'          => [null, true, new NullType],
            'null'           => [null, false, new NullType],
            '?integer'       => [1, true, new SimpleType('int', true, 1)],
            'integer'        => [1, false, new SimpleType('int', false, 1)],
            '?boolean-true'  => [true, true, new SimpleType('bool', true, true)],
            '?boolean-false' => [false, true, new SimpleType('bool', true, false)],
            'true'           => [true, false, new TrueType],
            'false'          => [false, false, new FalseType],
            '?object'        => [new stdClass, true, new ObjectType(TypeName::fromQualifiedName(stdClass::class), true)],
            'object'         => [new stdClass, false, new ObjectType(TypeName::fromQualifiedName(stdClass::class), false)],
        ];
    }

    /**
     * @return non-empty-array<string, array{0: Type, 1: non-empty-string, 2: bool}>
     */
    public static function namesToTypes(): array
    {
        return [
            '?void'             => [new VoidType, 'void', true],
            'void'              => [new VoidType, 'void', false],
            '?null'             => [new NullType, 'null', true],
            'null'              => [new NullType, 'null', true],
            '?int'              => [new SimpleType('int', true), 'int', true],
            '?integer'          => [new SimpleType('int', true), 'integer', true],
            'int'               => [new SimpleType('int', false), 'int', false],
            'bool'              => [new SimpleType('bool', false), 'bool', false],
            'boolean'           => [new SimpleType('bool', false), 'boolean', false],
            'true'              => [new TrueType, 'true', false],
            'false'             => [new FalseType, 'false', false],
            'object'            => [new GenericObjectType(false), 'object', false],
            'real'              => [new SimpleType('float', false), 'real', false],
            'double'            => [new SimpleType('float', false), 'double', false],
            'float'             => [new SimpleType('float', false), 'float', false],
            'string'            => [new SimpleType('string', false), 'string', false],
            'array'             => [new SimpleType('array', false), 'array', false],
            'resource'          => [new SimpleType('resource', false), 'resource', false],
            'resource (closed)' => [new SimpleType('resource (closed)', false), 'resource (closed)', false],
            'unknown type'      => [new UnknownType, 'unknown type', false],
            '?classname'        => [new ObjectType(TypeName::fromQualifiedName(stdClass::class), true), stdClass::class, true],
            'classname'         => [new ObjectType(TypeName::fromQualifiedName(stdClass::class), false), stdClass::class, false],
            'callable'          => [new CallableType(false), 'callable', false],
            '?callable'         => [new CallableType(true), 'callable', true],
            'iterable'          => [new IterableType(false), 'iterable', false],
            '?iterable'         => [new IterableType(true), 'iterable', true],
            'mixed'             => [new MixedType, 'mixed', false],
            'never'             => [new NeverType, 'never', false],
        ];
    }

    #[DataProvider('valuesToNullableType')]
    public function testTypeMappingFromValue(mixed $value, bool $allowsNull, Type $expectedType): void
    {
        $this->assertEquals($expectedType, Type::fromValue($value, $allowsNull));
    }

    /**
     * @param non-empty-string $typeName
     */
    #[DataProvider('namesToTypes')]
    public function testTypeMappingFromName(Type $expectedType, string $typeName, bool $allowsNull): void
    {
        $this->assertEquals($expectedType, Type::fromName($typeName, $allowsNull));
    }
}
