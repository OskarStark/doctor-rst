<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

class KernelInsteadOfAppKernel implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (strstr($line, 'app/AppKernel.php')) {
            return 'Please use "src/Kernel.php" instead of "app/AppKernel.php"';
        }

        if (strstr($line, 'AppKernel')) {
            return 'Please use "Kernel" instead of "AppKernel"';
        }
    }
}
