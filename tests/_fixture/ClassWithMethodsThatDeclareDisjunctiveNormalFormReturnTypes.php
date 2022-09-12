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

class ClassWithMethodsThatDeclareDisjunctiveNormalFormReturnTypes
{
    public function one(): (A&B)|D
    {
    }

    public function two(): C|(X&D)|null
    {
    }

    public function three(): (A&B&D)|int|null
    {
    }
}
