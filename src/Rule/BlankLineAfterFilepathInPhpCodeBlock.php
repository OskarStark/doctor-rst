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

/**
 * @Description("Make sure you have a blank line after a filepath in a PHP code block.")
 */
class BlankLineAfterFilepathInPhpCodeBlock extends AbstractRule implements Rule
{
    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
        ) {
            return;
        }

        $lines->next();
        $lines->next();

        // PHP
        if (preg_match('/^\/\/(.*)\.php$/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }
    }

    private function validateBlankLine(\ArrayIterator $lines, array $matches)
    {
        $lines->next();

        if (!RstParser::isBlankLine($lines->current()) && !PhpHelper::isComment($lines->current())) {
            return sprintf('Please add a blank line after "%s"', trim($matches[0]));
        }
    }
}
