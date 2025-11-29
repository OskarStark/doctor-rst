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
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Ensure link lines are at the bottom of the file.')]
final class EnsureLinkBottom extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::isLinkDefinition($line)) {
            return NullViolation::create();
        }

        while ($lines->valid()) {
            $lines->next();

            if (!$lines->valid()) {
                break;
            }

            $current = $lines->current();

            if ($current->isBlank()) {
                continue;
            }

            if (!RstParser::isLinkDefinition($current)) {
                return Violation::from(
                    'Please move link definition to the bottom of the page',
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        return NullViolation::create();
    }
}
