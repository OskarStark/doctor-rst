<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Helper;

use App\Value\Line;

final class XmlHelper
{
    public static function isComment(Line $line, ?bool $closed = null): bool
    {
        $string = $line->clean()->toString();

        if ('<!--' === $string || '-->' === $string) {
            return true;
        }

        if (null === $closed) {
            if (preg_match('/^<!--(.*)/', $string)) {
                return true;
            }
        } elseif (preg_match('/^<!--(.*)/', $string)
            && (
                ($closed && preg_match('/(.*)-->$/', $string))
                || (
                    !$closed && !preg_match('/(.*)-->$/', $string)
                )
            )) {
            return true;
        }

        return false;
    }
}
