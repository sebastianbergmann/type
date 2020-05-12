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
 * @covers \SebastianBergmann\Type\TypeName
 */
final class TypeNameTest extends TestCase
{
    public function testFromReflection(): void
    {
        $class    = new \ReflectionClass(TypeName::class);
        $typeName = TypeName::fromReflection($class);

        $this->assertTrue($typeName->isNamespaced());
        $this->assertEquals('SebastianBergmann\\Type', $typeName->namespaceName());
        $this->assertEquals(TypeName::class, $typeName->qualifiedName());
        $this->assertEquals('TypeName', $typeName->simpleName());
    }

    public function testFromQualifiedName(): void
    {
        $typeName = TypeName::fromQualifiedName('PHPUnit\\Framework\\MockObject\\TypeName');

        $this->assertTrue($typeName->isNamespaced());
        $this->assertEquals('PHPUnit\\Framework\\MockObject', $typeName->namespaceName());
        $this->assertEquals('PHPUnit\\Framework\\MockObject\\TypeName', $typeName->qualifiedName());
        $this->assertEquals('TypeName', $typeName->simpleName());
    }

    public function testFromQualifiedNameWithLeadingSeparator(): void
    {
        $typeName = TypeName::fromQualifiedName('\\Foo\\Bar');

        $this->assertTrue($typeName->isNamespaced());
        $this->assertEquals('Foo', $typeName->namespaceName());
        $this->assertEquals('Foo\\Bar', $typeName->qualifiedName());
        $this->assertEquals('Bar', $typeName->simpleName());
    }

    public function testFromQualifiedNameWithoutNamespace(): void
    {
        $typeName = TypeName::fromQualifiedName('Bar');

        $this->assertFalse($typeName->isNamespaced());
        $this->assertNull($typeName->namespaceName());
        $this->assertEquals('Bar', $typeName->qualifiedName());
        $this->assertEquals('Bar', $typeName->simpleName());
    }
}
