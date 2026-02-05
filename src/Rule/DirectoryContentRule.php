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

namespace App\Rule;

use App\Value\ViolationInterface;
use Symfony\Component\Finder\Finder;

/**
 * Rules using this interface are run once for the entire analyzed directory,
 * and have access to all files via the Finder. They are useful for cross-file
 * validation such as finding unused include files.
 */
interface DirectoryContentRule extends Rule
{
    /**
     * @return ViolationInterface[]
     */
    public function check(Finder $finder, string $analyzeDir): array;
}
