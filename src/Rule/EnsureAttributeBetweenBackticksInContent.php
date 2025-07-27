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
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Make sure to use backticks around attributes in content')]
#[InvalidExample('Use #[Route] to define route')]
#[ValidExample('Use ``#[Route]`` to define route')]
class EnsureAttributeBetweenBackticksInContent extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            || RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS, true)
            || RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ATTRIBUTES, true)
            || RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_SYMFONY, true)
            || RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_STANDALONE, true)
            || RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_PHP, true)
        ) {
            return NullViolation::create();
        }

        if ($line->raw()->match('/(?<!`)#\[[^\]]*\](?!`)/')) {
            return Violation::from(
                \sprintf('Please ensure to use backticks "%s"', $line->raw()->toString()),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
