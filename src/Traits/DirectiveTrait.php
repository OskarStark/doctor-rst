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

namespace App\Traits;

use App\Rst\RstParser;
use App\Rst\Value\DirectiveContent;
use App\Value\Lines;

trait DirectiveTrait
{
    public function getLineNumberOfDirective(string $directive, Lines $lines, int $number): int
    {
        $lines->seek($number);
        $startingLine = $lines->current();

        while ((
            $lines->current()->indention() === $startingLine->indention()
            || $lines->current()->isBlank()
        ) && !$lines->current()->isDirective()
        ) {
            $lines->previous();
        }

        if ($lines->valid()
            && $lines->current()->isDirective()
            && RstParser::directiveIs($lines->current(), $directive)
        ) {
            return $lines->key();
        }

        throw new \RuntimeException(\sprintf('Directive "%s" not found', $directive));
    }

    private function getDirectiveContent(string $directive, Lines $lines, int $number): DirectiveContent
    {
        $content = [];

        $number = $this->getLineNumberOfDirective($directive, $lines, $number);

        $lines->seek($number);

        $lines->next();

        $startingLine = $lines->current();

        if ($lines->current()->isBlank()) {
            $lines->next();
            $startingLine = $lines->current();
        }

        while ($lines->valid()) {
            if ($startingLine->indention() > $lines->current()->indention()
                && !$lines->current()->isBlank()) {
                break;
            }

            $content[] = $lines->current()->raw()->toString();

            $lines->next();
        }

        return new DirectiveContent($content);
    }

    private function inPhpCodeBlock(Lines $lines, int $number): bool
    {
        return $this->in(
            RstParser::DIRECTIVE_CODE_BLOCK,
            $lines,
            $number,
            [
                RstParser::CODE_BLOCK_PHP,
                RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
                RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
                RstParser::CODE_BLOCK_PHP_STANDALONE,
                RstParser::CODE_BLOCK_PHP_SYMFONY,
            ],
        );
    }

    private function inShellCodeBlock(Lines $lines, int $number): bool
    {
        return $this->in(
            RstParser::DIRECTIVE_CODE_BLOCK,
            $lines,
            $number,
            [
                RstParser::CODE_BLOCK_BASH,
                RstParser::CODE_BLOCK_SHELL,
                RstParser::CODE_BLOCK_TERMINAL,
            ],
        );
    }

    /**
     * @param null|string[] $directiveTypes
     */
    private function in(string $directive, Lines $lines, int $number, ?array $directiveTypes = null): bool
    {
        $lines->seek($number);

        $currentIndention = $lines->current()->indention();

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);
            $currentLine = $lines->current();

            if ($currentLine->isBlank()) {
                continue;
            }

            if ($currentLine->isHeadline()) {
                return false;
            }

            $lineIndention = $currentLine->indention();

            if ($lineIndention < $currentIndention
                && $currentLine->isDirective()
            ) {
                if (RstParser::directiveIs($currentLine, $directive)) {
                    if (null !== $directiveTypes) {
                        return array_any($directiveTypes, static fn ($type) => RstParser::codeBlockDirectiveIsTypeOf($currentLine, $type));
                    }

                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * @param null|string[] $directiveTypes
     */
    private function previousDirectiveIs(string $directive, Lines $lines, int $number, ?array $directiveTypes = null): bool
    {
        $lines->seek($number);

        $initialIndention = $lines->current()->indention();

        $i = $number;

        while (1 <= $i) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            $lineIndention = $lines->current()->indention();

            if ($lineIndention < $initialIndention) {
                return false;
            }

            if ($lineIndention === $initialIndention && !$lines->current()->isDirective()) {
                return false;
            }

            if ((
                $lineIndention === $initialIndention
                && $lines->current()->isDirective()
                && RstParser::directiveIs($lines->current(), $directive)
            ) || (0 === $lineIndention
                && (
                    RstParser::codeBlockDirectiveIsTypeOf($lines->current(), RstParser::CODE_BLOCK_PHP)
                    || RstParser::directiveIs($lines->current(), $directive)
                ))
            ) {
                if (null !== $directiveTypes) {
                    return array_any($directiveTypes, static fn ($type) => RstParser::codeBlockDirectiveIsTypeOf($lines->current(), $type));
                }

                return true;
            }
        }

        return false;
    }
}
