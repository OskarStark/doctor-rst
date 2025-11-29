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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
final class NoNamespaceAfterUseStatements extends AbstractRule implements LineContentRule
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

        if (!RstParser::isPhpDirective($line)
        ) {
            return NullViolation::create();
        }

        $indention = $line->indention();

        $lines->next();

        $useStatementFound = false;

        while ($lines->valid()
            && !$lines->current()->isDirective()
            && ($lines->current()->indention() > $indention || $lines->current()->isBlank())
        ) {
            $line = $lines->current();

            if ($line->clean()->match('/^use (.*);$/')) {
                $useStatementFound = true;
            }

            if ($line->clean()->match('/^namespace (.*);$/')) {
                if ($useStatementFound) {
                    return Violation::from(
                        'Please move the namespace before the use statement(s)',
                        $filename,
                        $number + 1,
                        $line,
                    );
                }

                break;
            }

            $lines->next();
        }

        return NullViolation::create();
    }
}
