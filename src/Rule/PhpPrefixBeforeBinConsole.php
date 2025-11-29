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

use App\Attribute\Rule\Description;
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Ensure `bin/console` is prefixed with `php` to be safe executable on Microsoft Windows.')]
#[InvalidExample('bin/console list')]
#[ValidExample('php bin/console list')]
final class PhpPrefixBeforeBinConsole extends AbstractRule implements LineContentRule
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

        if (preg_match('/((`|"|_|├─ |\/\/ )|\[\')bin\/console/u', $line->raw()->toString())
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
            return Violation::from(
                'Please add "php" prefix before "bin/console"',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
