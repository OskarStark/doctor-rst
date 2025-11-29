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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Make sure yarn `--dev` option for `add` command is used at the end.')]
#[InvalidExample('yarn add --dev jquery')]
#[ValidExample('yarn add jquery --dev')]
final class YarnDevOptionAtTheEnd extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/yarn add \-\-dev(.*)$/')) {
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
