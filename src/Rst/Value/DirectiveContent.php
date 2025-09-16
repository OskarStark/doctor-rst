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

namespace App\Rst\Value;

/**
 * @no-named-arguments
 */
final class DirectiveContent
{
    public array $cleaned = [];

    public function __construct(
        public readonly array $raw,
    ) {
        $cleaned = [];

        // remove entries in the array which equals an empty string, but only at the start and at the end
        foreach ($this->raw as $line) {
            if (0 === \count($cleaned) && '' === $line) {
                continue;
            }

            $cleaned[] = $line;
        }

        // reverse $cleaned array to remove empty lines at the end
        $cleaned = array_reverse($cleaned);

        foreach ($cleaned as $key => $line) {
            if ('' === $line) {
                unset($cleaned[$key]);
            } else {
                break;
            }
        }

        // reverse again to get the original order
        $cleaned = array_reverse($cleaned);

        $this->cleaned = $cleaned;
    }

    public function numberOfLines(): int
    {
        return \count($this->cleaned);
    }
}
