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

namespace App\Value;

final class Lines
{
    private $array;

    /**
     * @param array<int, string> $array
     */
    private function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * @param array<int, string> $array
     */
    public static function fromArray(array $array): self
    {
        return new self($array);
    }

    /**
     * @return \ArrayIterator<int, string>
     */
    public function toIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->array);
    }

    /**
     * @return array<int, string>
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
