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
use App\Helper\XmlHelper;
use App\Rst\RstParser;
use App\Value\Lines;

/**
 * @Description("Make sure you have a blank line after a filepath in a XML code block.")
 */
class BlankLineAfterFilepathInXmlCodeBlock extends AbstractRule implements Rule
{
    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_XML)) {
            return null;
        }

        $lines->next();
        $lines->next();

        // XML
        if (preg_match('/^<!--(.*)\.(xml|xlf|xliff)(.*)-->$/', $lines->current()->clean(), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        return null;
    }

    private function validateBlankLine(Lines $lines, array $matches): ?string
    {
        $lines->next();

        if (!$lines->current()->isBlank() && !XmlHelper::isComment($lines->current())) {
            return sprintf('Please add a blank line after "%s"', trim($matches[0]));
        }

        return null;
    }
}
