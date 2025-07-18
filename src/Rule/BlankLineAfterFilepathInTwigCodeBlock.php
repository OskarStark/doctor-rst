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
use App\Helper\TwigHelper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Make sure you have a blank line after a filepath in a Twig code block.')]
class BlankLineAfterFilepathInTwigCodeBlock extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TWIG)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_TWIG)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_JINJA)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_JINJA)
        ) {
            return NullViolation::create();
        }

        $lines->next();
        ++$number;

        $lines->next();
        ++$number;

        // TWIG
        if ($matches = $lines->current()->clean()->match('/^{#(.*)\.twig(.*)#}/')) {
            /** @var string[] $matches */
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

        if (!$lines->current()->isBlank() && !TwigHelper::isComment($lines->current())) {
            $match = trim((string) $matches[0]);

            return Violation::from(
                \sprintf('Please add a blank line after "%s"', $match),
                $filename,
                $number + 1,
                $match,
            );
        }

        return NullViolation::create();
    }
}
