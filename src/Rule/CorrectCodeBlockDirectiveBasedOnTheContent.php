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

class CorrectCodeBlockDirectiveBasedOnTheContent extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return;
        }

        $indention = RstParser::indention($line);

        // check code-block: twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TWIG, true)) {
            $lines->next();

            while ($lines->valid()
                && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
            ) {
                if (preg_match('/[<]+/', RstParser::clean($lines->current()), $matches)
                    && !preg_match('/<3/', RstParser::clean($lines->current()))
                ) {
                    return $this->getErrorMessage(RstParser::CODE_BLOCK_HTML_TWIG, RstParser::CODE_BLOCK_TWIG);
                }

                $lines->next();
            }
        }

        // check code-block: html+twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_TWIG, true)) {
            $lines->next();

            $foundHtml = false;

            while ($lines->valid()
                && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
                && false === $foundHtml
            ) {
                if (preg_match('/[<]+/', RstParser::clean($lines->current()))) {
                    $foundHtml = true;
                }

                $lines->next();
            }

            if (!$foundHtml) {
                return $this->getErrorMessage(RstParser::CODE_BLOCK_TWIG, RstParser::CODE_BLOCK_HTML_TWIG);
            }
        }
    }

    private function getErrorMessage(string $new, string $current): string
    {
        return sprintf('Please use "%s" instead of "%s"', $new, $current);
    }
}
