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
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

#[CoversClass(TypeName::class)]
#[Small]
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
        $typeName = TypeName::fromQualifiedName(self::class);

        $this->assertTrue($typeName->isNamespaced());
        $this->assertSame('SebastianBergmann\\Type', $typeName->namespaceName());
        $this->assertSame('SebastianBergmann\\Type\\TypeNameTest', $typeName->qualifiedName());
        $this->assertSame('TypeNameTest', $typeName->simpleName());
    }

    public function testFromQualifiedNameWithLeadingSeparator(): void
    {
        $typeName = TypeName::fromQualifiedName('\\' . self::class);

        $this->assertTrue($typeName->isNamespaced());
        $this->assertSame('SebastianBergmann\\Type', $typeName->namespaceName());
        $this->assertSame('SebastianBergmann\\Type\\TypeNameTest', $typeName->qualifiedName());
        $this->assertSame('TypeNameTest', $typeName->simpleName());
    }

    public function testFromQualifiedNameWithoutNamespace(): void
    {
        $typeName = TypeName::fromQualifiedName(stdClass::class);

        $this->assertFalse($typeName->isNamespaced());
        $this->assertNull($typeName->namespaceName());
        $this->assertSame(stdClass::class, $typeName->qualifiedName());
        $this->assertSame(stdClass::class, $typeName->simpleName());
    }
}
