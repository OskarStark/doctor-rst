<?php

declare(strict_types=1);

/*
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use App\Annotations\Rule\Description;
use App\Helper\PhpHelper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Make sure you have a blank line after a filepath in a PHP code block.")
 */
class BlankLineAfterFilepathInPhpCodeBlock extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ATTRIBUTES)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_SYMFONY)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_STANDALONE)
        ) {
            return NullViolation::create();
        }

        $lines->next();
        $lines->next();

        // PHP
        if ($matches = $lines->current()->clean()->match('/^\/\/(.*)\.php$/')) {
            return $this->validateBlankLine($lines, $matches, $filename, $number);
        }

        return NullViolation::create();
    }

    private function validateBlankLine(Lines $lines, array $matches, string $filename, int $number): ViolationInterface
    {
        $lines->next();

        if (!$lines->current()->isBlank() && !PhpHelper::isComment($lines->current())) {
            $match = trim($matches[0]);

            return Violation::from(
                sprintf('Please add a blank line after "%s"', $match),
                $filename,
                $number + 1,
                $match
            );
        }

        return NullViolation::create();
    }
}
