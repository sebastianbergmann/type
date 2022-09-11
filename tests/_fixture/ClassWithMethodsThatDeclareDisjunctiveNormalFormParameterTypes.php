<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type\TestFixture;

final class ClassWithMethodsThatDeclareDisjunctiveNormalFormParameterTypes
{
    public function dnfOne((A&B)|D $x): void
    {
    }

    public function dnfTwo(C|(X&D)|null $x): void
    {
    }

    public function dnfThree((A&B&D)|int|null $x): void
    {
    }
}
