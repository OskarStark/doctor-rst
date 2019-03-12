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

use App\Rst\RstParser;

final class TwigHelper
{
    public static function isComment(string $line): bool
    {
        if (preg_match('/^{#(.*)/', RstParser::clean($line))) {
            return true;
        }

        return false;
    }
}
