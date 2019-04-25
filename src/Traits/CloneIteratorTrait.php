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

namespace App\Traits;

trait CloneIteratorTrait
{
    private function cloneIterator(\ArrayIterator $iterator, int $number): \ArrayIterator
    {
        $clone = new \ArrayIterator($iterator->getArrayCopy());
        $clone->seek($number);

        return $clone;
    }
}
