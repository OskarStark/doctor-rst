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

class NoExplicitUseOfCodeBlockPhp implements Rule
{
    public static function getName(): string
    {
        return 'no_explicit_use_of_code_block_php';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);

        // only interesting if a PHP code block
        if (!RstParser::codeBlockDirectiveIsTypeOf($lines->current(), RstParser::CODE_BLOCK_PHP)) {
            return;
        }

        // :: is a php code block, but its ok
        if (preg_match('/\:\:$/', RstParser::clean($lines->current()))) {
            return;
        }

        // check if the code block is not on the first level, in this case
        // it could not be in a configuration block which would be ok
        if (preg_match('/^[\s]+/', $lines->current(), $matches)
            && RstParser::codeBlockDirectiveIsTypeOf($lines->current(), RstParser::CODE_BLOCK_PHP)
            && $number > 0
        ) {
            $currentIndention = mb_strlen($matches[0]);

            $i = $number;
            while ($i >= 1) {
                --$i;
                $lines->seek($i);
                $lineIndention = 0;

                if (RstParser::isBlankLine($lines->current())) {
                    goto next;
                }

                if (preg_match('/^[\s]+/', $lines->current(), $matches)) {
                    $lineIndention = mb_strlen($matches[0]);
                }

                if ($lineIndention < $currentIndention
                    && RstParser::isDirective($lines->current())
                ) {
                    if (RstParser::directiveIs($lines->current(), RstParser::DIRECTIVE_CONFIGURATION_BLOCK)) {
                        return;
                    }
                    goto error;
                }

                next:
            }
        }

        error:
        return 'Please do not use ".. code-block:: php", use "::" instead.';
    }
}
