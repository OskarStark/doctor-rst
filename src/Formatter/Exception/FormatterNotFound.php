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

namespace App\Formatter\Exception;

final class FormatterNotFound extends \InvalidArgumentException
{
    public static function byName(string $name): self
    {
        return new self(sprintf(
            'Formatter "%s" not found',
            $name
        ));
    }
}
