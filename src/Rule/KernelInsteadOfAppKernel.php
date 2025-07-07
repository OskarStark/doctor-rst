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

use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
class KernelInsteadOfAppKernel extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->raw()->match('/app\/AppKernel\.php/')) {
            return Violation::from(
                'Please use "src/Kernel.php" instead of "app/AppKernel.php"',
                $filename,
                $number + 1,
                $line,
            );
        }

        if ($line->raw()->match('/AppKernel/')) {
            return Violation::from(
                'Please use "Kernel" instead of "AppKernel"',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
