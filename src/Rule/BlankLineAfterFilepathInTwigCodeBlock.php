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

use App\Handler\RulesHandler;
use App\Rst\RstParser;

class BlankLineAfterFilepathInTwigCodeBlock implements Rule
{
    public static function getName(): string
    {
        return 'blank_line_after_filepath_in_twig_code_block';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_DEV];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TWIG)) {
            return;
        }

        $lines->next();
        $lines->next();

        // TWIG
        if (preg_match('/^{#(.*).twig(.*)#}/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }
    }

    private function validateBlankLine(\ArrayIterator $lines, array $matches)
    {
        $lines->next();

        if (!RstParser::isBlankLine($lines->current())) {
            return sprintf('Please add a blank line after "%s"', $matches[0]);
        }
    }
}
