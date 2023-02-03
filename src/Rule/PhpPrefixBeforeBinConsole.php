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
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Ensure `bin/console` is prefixed with `php` to be safe executable on Microsoft Windows.")
 *
 * @InvalidExample("bin/console list")
 *
 * @ValidExample("php bin/console list")
 */
class PhpPrefixBeforeBinConsole extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!preg_match('/bin\/console/', $line->raw()->toString())) {
            return NullViolation::create();
        }

        if (preg_match('/(`|"|_|├─ )bin\/console/u', $line->raw()->toString())
            || preg_match('/php "%s\/\.\.\/bin\/console"/', $line->raw()->toString())) {
            return NullViolation::create();
        }

        if (preg_match('@/bin/console:\d+@u', $line->raw()->toString())
            || preg_match('/php "%s\/\.\.\/bin\/console"/', $line->raw()->toString())) {
            return NullViolation::create();
        }

        if (RstParser::isLinkDefinition($line)) {
            return NullViolation::create();
        }

        if (!preg_match('/php(.*)bin\/console/', $line->raw()->toString())) {
            $message = 'Please add "php" prefix before "bin/console"';

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                ''
            );
        }

        return NullViolation::create();
    }
}
