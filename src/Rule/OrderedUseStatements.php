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

class OrderedUseStatements extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
        ) {
            return;
        }

        $indention = RstParser::indention($line);

        $lines->next();

        $useStatements = [];
        $indentionOfFirstFoundUseStatement = null;

        while ($lines->valid()
            && !RstParser::isDirective($lines->current())
            && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
        ) {
            if (preg_match('/use (.*);/', RstParser::clean($lines->current()), $matches)) {
                if (null === $indentionOfFirstFoundUseStatement) {
                    $indentionOfFirstFoundUseStatement = RstParser::indention($lines->current());
                    $useStatements[] = RstParser::clean($lines->current());
                } else {
                    if ($indentionOfFirstFoundUseStatement != RstParser::indention($lines->current())) {
                        break;
                    }

                    $useStatements[] = RstParser::clean($lines->current());
                }
            }

            $lines->next();
        }

        if (empty($useStatements)) {
            return;
        }

        $sortedUseStatements = $useStatements;
        sort($sortedUseStatements);

        if ($useStatements !== $sortedUseStatements) {
            dump($sortedUseStatements);
            return 'Please reorder the use statements alphabetical';
        }
    }
}
