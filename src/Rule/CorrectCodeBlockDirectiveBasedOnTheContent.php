<?php

declare(strict_types=1);

/**
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
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class CorrectCodeBlockDirectiveBasedOnTheContent extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return NullViolation::create();
        }

        $indention = $line->indention();

        // check code-block: twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TWIG, true)) {
            $lines->next();

            while ($lines->valid()
                && ($lines->current()->indention() > $indention || $lines->current()->isBlank())
            ) {
                if (preg_match('/[<]+/', $lines->current()->clean()->toString(), $matches)
                    && !preg_match('/<3/', $lines->current()->clean()->toString())
                ) {
                    return Violation::from(
                        $this->getErrorMessage(RstParser::CODE_BLOCK_HTML_TWIG, RstParser::CODE_BLOCK_TWIG),
                        $filename,
                        $number + 1,
                        $line,
                    );
                }

                $lines->next();
            }
        }

        // check code-block: html+twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_TWIG, true)) {
            $lines->next();

            $foundHtml = false;

            while ($lines->valid()
                && ($lines->current()->indention() > $indention || $lines->current()->isBlank())
                && false === $foundHtml
            ) {
                if (preg_match('/[<]+/', $lines->current()->clean()->toString())) {
                    $foundHtml = true;
                }

                $lines->next();
            }

            if (!$foundHtml) {
                return Violation::from(
                    $this->getErrorMessage(RstParser::CODE_BLOCK_TWIG, RstParser::CODE_BLOCK_HTML_TWIG),
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        return NullViolation::create();
    }

    private function getErrorMessage(string $new, string $current): string
    {
        return sprintf('Please use "%s" instead of "%s"', $new, $current);
    }
}
