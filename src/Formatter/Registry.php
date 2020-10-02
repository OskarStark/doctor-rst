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

namespace App\Formatter;

class Registry
{
    /** @var Formatter[] */
    private array $formatters = [];

    public function __construct(Formatter ...$formatters)
    {
        foreach ($formatters as $formatter) {
            $this->formatters[$formatter->name()] = $formatter;
        }
    }

    public function get(string $name): Formatter
    {
        if (!isset($this->formatters[$name])) {
            throw new \InvalidArgumentException(sprintf('Formatter "%s" not found', $name));
        }

        return $this->formatters[$name];
    }
}
