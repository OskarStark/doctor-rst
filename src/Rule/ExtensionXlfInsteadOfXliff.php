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

/**
 * @no-named-arguments
 */
#[Description('Make sure to only use `.xlf` instead of `.xliff`.')]
#[ValidExample('messages.xlf')]
#[InvalidExample('messages.xliff')]
final class ExtensionXlfInsteadOfXliff extends AbstractRule implements LineContentRule
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

        if ($matches = $line->raw()->match('/\.xliff/i')) {
            /** @var string[] $matches */
            return Violation::from(
                \sprintf('Please use ".xlf" extension instead of "%s"', $matches[0]),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
