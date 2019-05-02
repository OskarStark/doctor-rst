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
    public static function isComment(string $line, bool $closed = null): bool
    {
        $line = RstParser::clean($line);

        if ('{#' === $line || '#}' === $line) {
            return true;
        }

        if (null === $closed) {
            if (preg_match('/^{#(.*)/', $line)) {
                return true;
            }
        } else {
            if (preg_match('/^{#(.*)/', $line)
                && (
                    ($closed && preg_match('/(.*)#}$/', $line))
                    || (!$closed && !preg_match('/(.*)#}$/', $line)
                    )
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
