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

final class TwigHelper
{
    public static function isComment(Line $line, bool $closed = null): bool
    {
        $string = $line->clean();

        if ($string->equalsTo(['{#', '#}'])) {
            return true;
        }

        if (null === $closed && $string->startsWith('{#')) {
            return true;
        }

        return $string->startsWith('{#')
            && (
                ($closed && $string->endsWith('#}'))
                || (!$closed && !$string->endsWith('#}'))
            )
        ;
    }
}
