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

use App\Helper\XmlHelper;
use App\Rst\RstParser;

class BlankLineAfterFilepathInXmlCodeBlock extends AbstractRule implements Rule
{
    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_XML)) {
            return;
        }

        $lines->next();
        $lines->next();

        // XML
        if (preg_match('/^<!--(.*)\.xml(.*)-->$/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }
    }

    private function validateBlankLine(\ArrayIterator $lines, array $matches)
    {
        $lines->next();

        if (!RstParser::isBlankLine($lines->current()) && !XmlHelper::isComment($lines->current())) {
            return sprintf('Please add a blank line after "%s"', trim($matches[0]));
        }
    }
}
