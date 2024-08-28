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

namespace App\Tests;

use App\Value\Lines;

final readonly class RstSample
{
    public Lines $lines;

    /**
     * @param array<string>|string $content
     */
    public function __construct(
        array|string $content,
        public int $lineNumber = 0,
    ) {
        if (!\is_array($content)) {
            $content = explode(\PHP_EOL, $content);
        }

        $this->lines = Lines::fromArray($content);
    }
}
