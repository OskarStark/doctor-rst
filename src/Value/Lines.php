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

namespace App\Value;

final class Lines implements \SeekableIterator
{
    private int $currentLine = 0;

    /**
     * @param array<int, Line> $array
     */
    private function __construct(private array $array)
    {
    }

    public function __clone()
    {
        $this->rewind();
    }

    /**
     * @param array<int, string> $array
     */
    public static function fromArray(array $array): self
    {
        return new self(array_map(static fn (string $string) => new Line($string), $array));
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
        if (!isset($this->array[$this->currentLine])) {
            throw $this->createOutOfBoundException($this->currentLine);
        }

        return $this->array[$this->currentLine];
    }

    public function next(): void
    {
        ++$this->currentLine;
    }

    public function previous(): void
    {
        --$this->currentLine;
    }

    public function key(): int
    {
        if (!isset($this->array[$this->currentLine])) {
            throw $this->createOutOfBoundException($this->currentLine);
        }

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
        $currentLine = $this->currentLine;
        $this->currentLine = $line;

        if (!isset($this->array[$this->currentLine])) {
            $this->currentLine = $currentLine;

            throw $this->createOutOfBoundException($line);
        }
    }

    public function isProcessedBy(int $no, string $rule): bool
    {
        if (!isset($this->array[$no])) {
            return false;
        }

        return $this->array[$no]->isProcessedBy($rule);
    }

    private function createOutOfBoundException(int $line): \OutOfBoundsException
    {
        return new \OutOfBoundsException(\sprintf('Line "%d" does not exists.', $line));
    }
}
