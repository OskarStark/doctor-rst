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
use App\Helper\Helper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;
use function Symfony\Component\String\u;

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

            if ($this->containsHtml(Helper::cloneIterator($lines, (int) $lines->key()), $indention)) {
                return $this->getErrorMessage(RstParser::CODE_BLOCK_HTML_TWIG, RstParser::CODE_BLOCK_TWIG);
            }
        }

        // check code-block: html+twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_TWIG, true)) {
            $lines->next();

            if (!$this->containsHtml(Helper::cloneIterator($lines, (int) $lines->key()), $indention)) {
                return $this->getErrorMessage(RstParser::CODE_BLOCK_TWIG, RstParser::CODE_BLOCK_HTML_TWIG);
            }
        }

        return null;
    }

    public function containsHtml(\ArrayIterator $lines, int $indention): bool
    {
        $content = [];

        while ($lines->valid()
            && (($indention < RstParser::indention($lines->current()) || 0 === $indention) || RstParser::isBlankLine($lines->current()))
        ) {
            $content[] = RstParser::clean($lines->current());

            $lines->next();
        }

        /**
         * it looks like strip_tags is stripping a single "<3" (used often as heard),
         * so we replace it beforehand.
         */
        $string = u(implode(' ', $content))
            ->replace('<3', 'heart')
            ->trim()
        ;

        return $string->length() !== u(strip_tags($string->toString()))->length();
    }

    private function getErrorMessage(string $new, string $current): string
    {
        return sprintf('Please use "%s" instead of "%s"', $new, $current);
    }
}
