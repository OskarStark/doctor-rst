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

use App\Helper\PhpHelper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

class NoBlankLineAfterFilepathInPhpCodeBlock extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ATTRIBUTES)
        ) {
            return null;
        }

        $lines->next();
        $lines->next();

        // PHP
        if (preg_match('/^\/\/(.*)\.php$/', $lines->current()->clean(), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        return null;
    }

    private function validateBlankLine(Lines $lines, array $matches): ?string
    {
        $lines->next();

        if ($lines->current()->isBlank()) {
            $lines->next();
            if (!PhpHelper::isComment($lines->current())) {
                return sprintf('Please remove blank line after "%s"', trim($matches[0]));
            }
        }

        return null;
    }
}
