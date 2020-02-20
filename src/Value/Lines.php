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

final class Lines implements \SeekableIterator
{
    private array $array;
    private int $currentLine = 0;

    /**
     * @param array<int, Line> $array
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
        return new self(array_map(static function (string $string) {
            return new Line($string);
        }, $array));
    }

    /**
     * @return \ArrayIterator<int, Line>
     */
    public function toIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->array);
    }

    public function current(): Line
    {
        return $this->array[$this->currentLine];
    }

    public function next(): void
    {
        ++$this->currentLine;
    }

    public function key(): int
    {
        return $this->currentLine;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->currentLine]);
    }

    public function rewind(): void
    {
        $this->currentLine = 0;
    }

    /**
     * @param int $line
     */
    public function seek($line): void
    {
        $this->currentLine = $line;

        if (!$this->valid()) {
            throw new \OutOfBoundsException(
                sprintf(
                    'Line "%s" does not exists.',
                    $this->currentLine
                )
            );
        }
    }

    public function __clone()
    {
        $this->rewind();
    }
}
