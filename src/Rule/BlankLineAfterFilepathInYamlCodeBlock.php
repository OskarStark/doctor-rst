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
use App\Helper\YamlHelper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Make sure you have a blank line after a filepath in a YAML code block.')]
final class BlankLineAfterFilepathInYamlCodeBlock extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YAML)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YML)
        ) {
            return NullViolation::create();
        }

        $lines->next();
        ++$number;

        $lines->next();
        ++$number;

        // YML / YAML
        if ($matches = $lines->current()->clean()->match('/^#(.*)\.(yml|yaml)$/')) {
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

        if (!$lines->current()->isBlank() && !YamlHelper::isComment($lines->current())) {
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
