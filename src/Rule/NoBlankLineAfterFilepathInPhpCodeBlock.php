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

use App\Helper\PhpHelper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
final class NoBlankLineAfterFilepathInPhpCodeBlock extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::isPhpDirective($line)) {
            return NullViolation::create();
        }

        $lines->next();
        ++$number;

        $lines->next();
        ++$number;

        // PHP
        if (preg_match('/^\/\/(.*)\.php$/', $lines->current()->clean()->toString(), $matches)) {
            return self::validateBlankLine($lines, $matches, $filename, $number);
        }

        return NullViolation::create();
    }

    /**
     * @param string[] $matches
     */
    private static function validateBlankLine(Lines $lines, array $matches, string $filename, int $number): ViolationInterface
    {
        $lines->next();

        if ($lines->current()->isBlank()) {
            $lines->next();

            if (!PhpHelper::isComment($lines->current())) {
                $match = trim((string) $matches[0]);

                return Violation::from(
                    \sprintf('Please remove blank line after "%s"', $match),
                    $filename,
                    $number + 1,
                    $match,
                );
            }
        }

        return NullViolation::create();
    }
}
