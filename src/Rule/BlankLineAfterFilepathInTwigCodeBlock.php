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
use App\Helper\TwigHelper;
use App\Rst\RstParser;
use App\Value\Lines;

/**
 * @Description("Make sure you have a blank line after a filepath in a Twig code block.")
 */
class BlankLineAfterFilepathInTwigCodeBlock extends AbstractRule implements Rule
{
    public function check(Lines $lines, int $number): ?string
    {
        $lines = $lines->toIterator();

        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TWIG)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_TWIG)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_JINJA)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_JINJA)
        ) {
            return null;
        }

        $lines->next();
        $lines->next();

        // TWIG
        if (preg_match('/^{#(.*)\.twig(.*)#}/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        return null;
    }

    private function validateBlankLine(\ArrayIterator $lines, array $matches): ?string
    {
        $lines->next();

        if (!RstParser::isBlankLine($lines->current()) && !TwigHelper::isComment($lines->current())) {
            return sprintf('Please add a blank line after "%s"', trim($matches[0]));
        }

        return null;
    }
}
