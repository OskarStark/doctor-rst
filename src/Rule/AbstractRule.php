<?php

declare(strict_types=1);

/*
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use Doctrine\Common\Inflector\Inflector;

abstract class AbstractRule
{
    public static function getName(): string
    {
        return Inflector::tableize(substr(strrchr(static::class, '\\'), 1));
    }

    public static function getGroups(): array
    {
        return [];
    }
}
