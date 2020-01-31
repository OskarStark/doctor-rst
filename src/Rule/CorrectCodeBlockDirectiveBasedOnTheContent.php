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

use App\Handler\Registry;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

class CorrectCodeBlockDirectiveBasedOnTheContent extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines = $lines->toIterator();

        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return null;
        }

        $indention = RstParser::indention($line);

        // check code-block: twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TWIG, true)) {
            $lines->next();

            $foundHtml = 0;

            while ($lines->valid()
                && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
            ) {
                if (preg_match('/[<|>]+/', RstParser::clean($lines->current()), $matches)
                    && !preg_match('/<3/', RstParser::clean($lines->current()))
                ) {
                    ++$foundHtml;
                }

                $lines->next();
            }

            if (0 === ($foundHtml % 2)) {
                return $this->getErrorMessage(RstParser::CODE_BLOCK_HTML_TWIG, RstParser::CODE_BLOCK_TWIG);
            }
        }

        // check code-block: html+twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_TWIG, true)) {
            $lines->next();

            $foundHtml = 0;

            while ($lines->valid()
                && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
            ) {
                if (preg_match('/[<|>]+/', RstParser::clean($lines->current()), $matches)) {
                    ++$foundHtml;
                }

                $lines->next();
            }

            /*
             * Because online one could be a comparator like:
             *
             *     {% if item.stock < 10 %}
             */
            if (0 !== ($foundHtml % 2)) {
                return $this->getErrorMessage(RstParser::CODE_BLOCK_TWIG, RstParser::CODE_BLOCK_HTML_TWIG);
            }
        }

        return null;
    }

    private function getErrorMessage(string $new, string $current): string
    {
        return sprintf('Please use "%s" instead of "%s"', $new, $current);
    }
}
