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

use SebastianBergmann\Type\TestFixture\ParentClass;
use SebastianBergmann\Type\TestFixture\ParentClassAlias;

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
        $this->assertEquals('SebastianBergmann\\Type', $typeName->getNamespaceName());
        $this->assertEquals(TypeName::class, $typeName->getQualifiedName());
        $this->assertEquals('TypeName', $typeName->getSimpleName());
    }

    public function testFromQualifiedName(): void
    {
        $typeName = TypeName::fromQualifiedName('PHPUnit\\Framework\\MockObject\\TypeName');

        $this->assertTrue($typeName->isNamespaced());
        $this->assertEquals('PHPUnit\\Framework\\MockObject', $typeName->getNamespaceName());
        $this->assertEquals('PHPUnit\\Framework\\MockObject\\TypeName', $typeName->getQualifiedName());
        $this->assertEquals('TypeName', $typeName->getSimpleName());
    }

    public function testFromQualifiedNameWithLeadingSeparator(): void
    {
        $typeName = TypeName::fromQualifiedName('\\Foo\\Bar');

        $this->assertTrue($typeName->isNamespaced());
        $this->assertEquals('Foo', $typeName->getNamespaceName());
        $this->assertEquals('Foo\\Bar', $typeName->getQualifiedName());
        $this->assertEquals('Bar', $typeName->getSimpleName());
    }

    public function testFromQualifiedNameWithoutNamespace(): void
    {
        $typeName = TypeName::fromQualifiedName('Bar');

        $this->assertFalse($typeName->isNamespaced());
        $this->assertNull($typeName->getNamespaceName());
        $this->assertEquals('Bar', $typeName->getQualifiedName());
        $this->assertEquals('Bar', $typeName->getSimpleName());
    }

    public function testCannonicalWithClassNotExist(): void
    {
        $typeName = TypeName::fromQualifiedName('Bar');

        $this->assertEquals(true, $typeName->isCanonical());
        $this->assertEquals('Bar', $typeName->getCanonicalName());
    }

    public function testCannonicalWithClassExist(): void
    {
        $typeName = TypeName::fromQualifiedName(ParentClass::class);

        $this->assertEquals(true, $typeName->isCanonical());
        $this->assertEquals(ParentClass::class, $typeName->getQualifiedName());
        $this->assertEquals(ParentClass::class, $typeName->getCanonicalName());
    }

    public function testCannonicalWithClassAlias(): void
    {
        $typeName = TypeName::fromQualifiedName(ParentClassAlias::class);

        $this->assertEquals(false, $typeName->isCanonical());
        $this->assertEquals(ParentClassAlias::class, $typeName->getQualifiedName());
        $this->assertEquals(ParentClass::class, $typeName->getCanonicalName());
    }
}
