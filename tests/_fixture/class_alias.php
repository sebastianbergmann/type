<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class_alias(
    SebastianBergmann\Type\TestFixture\ClassThatHasAlias::class,
    'SebastianBergmann\Type\TestFixture\AliasOfClassThatHasAlias',
);

class_alias(
    SebastianBergmann\Type\TestFixture\ChildOfClassThatHasAlias::class,
    'SebastianBergmann\Type\TestFixture\AliasOfChildOfClassThatHasAlias',
);
