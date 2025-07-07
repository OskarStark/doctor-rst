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

/**
 * @no-named-arguments
 */
final readonly class FileResult
{
    public function __construct(
        private \SplFileInfo $file,
        private ExcludedViolationList $violationList,
    ) {
    }

    public function filename(): string
    {
        return (string) $this->file->getRealPath();
    }

    public function violationList(): ExcludedViolationList
    {
        return $this->violationList;
    }
}
