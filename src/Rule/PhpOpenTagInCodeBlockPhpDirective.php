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

use App\Rst\RstParser;
use App\Value\Lines;

class PhpOpenTagInCodeBlockPhpDirective extends AbstractRule implements LineContentRule
{
    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP, true)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS, true)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ATTRIBUTES, true)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_SYMFONY, true)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_STANDALONE, true)
        ) {
            return null;
        }

        $lines->next();
        $lines->next();

        // check if next line is "<?php"
        $nextLine = $lines->current();

        if (!$nextLine->clean()->startsWith('<?php')) {
            return sprintf('Please add PHP open tag after "%s" directive', $line->raw()->toString());
        }

        return null;
    }
}
