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

class SpaceBeforeSelfXmlClosingTag extends AbstractRule implements Rule
{
    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!preg_match('/\/>/', $line)) {
            return;
        }

        if (!preg_match('/\ \/>/', $line)) {
            return 'Please add space before "/>"';
        }
    }
}
