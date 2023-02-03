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

namespace App\Rule;

use App\Annotations\Rule\Description;
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Ensure `bin/console` is not prefixed with `php`.")
 *
 * @InvalidExample("php bin/console list")
 *
 * @ValidExample("bin/console list")
 */
class NoPhpPrefixBeforeBinConsole extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->raw()->match('/php bin\/console/')) {
            return Violation::from(
                'Please remove "php" prefix before "bin/console"',
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }
}
