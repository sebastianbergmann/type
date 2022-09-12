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
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameter::class)]
#[UsesClass(SimpleType::class)]
#[UsesClass(Type::class)]
#[Small]
final class ParameterTest extends TestCase
{
    public function testHasName(): void
    {
        $parameter = new Parameter('name', Type::fromValue(1, false));

        $this->assertSame('name', $parameter->name());
    }

    public function testHasType(): void
    {
        $type      = Type::fromValue(1, false);
        $parameter = new Parameter('name', $type);

        $this->assertSame($type, $parameter->type());
    }
}
