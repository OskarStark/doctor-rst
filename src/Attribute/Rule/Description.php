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

namespace App\Attribute\Rule;

/**
 * @no-named-arguments
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Description
{
    public function __construct(
        public readonly string $value,
    ) {
    }
}
