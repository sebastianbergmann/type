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
use ReflectionClass;

/**
 * @covers \SebastianBergmann\Type\TypeName
 */
final class TypeNameTest extends TestCase
{
    public function testFromReflection(): void
    {
        $class    = new ReflectionClass(TypeName::class);
        $typeName = TypeName::fromReflection($class);

        $this->assertTrue($typeName->isNamespaced());
        $this->assertSame('SebastianBergmann\\Type', $typeName->namespaceName());
        $this->assertSame(TypeName::class, $typeName->qualifiedName());
        $this->assertSame('TypeName', $typeName->simpleName());
    }

    public function testFromQualifiedName(): void
    {
        $typeName = TypeName::fromQualifiedName('PHPUnit\\Framework\\MockObject\\TypeName');

        $this->assertTrue($typeName->isNamespaced());
        $this->assertSame('PHPUnit\\Framework\\MockObject', $typeName->namespaceName());
        $this->assertSame('PHPUnit\\Framework\\MockObject\\TypeName', $typeName->qualifiedName());
        $this->assertSame('TypeName', $typeName->simpleName());
    }

    public function testFromQualifiedNameWithLeadingSeparator(): void
    {
        $typeName = TypeName::fromQualifiedName('\\Foo\\Bar');

        $this->assertTrue($typeName->isNamespaced());
        $this->assertSame('Foo', $typeName->namespaceName());
        $this->assertSame('Foo\\Bar', $typeName->qualifiedName());
        $this->assertSame('Bar', $typeName->simpleName());
    }

    public function testFromQualifiedNameWithoutNamespace(): void
    {
        $typeName = TypeName::fromQualifiedName('Bar');

        $this->assertFalse($typeName->isNamespaced());
        $this->assertNull($typeName->namespaceName());
        $this->assertSame('Bar', $typeName->qualifiedName());
        $this->assertSame('Bar', $typeName->simpleName());
    }
}
