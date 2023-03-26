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

use App\Annotations\Rule\Description;
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Make sure Composer `--dev` option for `require` command is used at the end.")
 *
 * @InvalidExample("composer require --dev symfony/var-dumper")
 *
 * @ValidExample("composer require symfony/var-dumper --dev")
 */
class ComposerDevOptionAtTheEnd extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/composer require \-\-dev(.*)$/')) {
            return Violation::from(
                'Please move "--dev" option to the end of the command',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
