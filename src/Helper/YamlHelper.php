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

namespace App\Helper;

use App\Value\Line;

final class YamlHelper
{
    public static function isComment(Line $line): bool
    {
        if (preg_match('/^#(.*)/', $line->clean())) {
            return true;
        }

        return false;
    }
}
