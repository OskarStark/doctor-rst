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
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class LowercaseAsInUseStatements extends AbstractRule implements LineContentRule
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

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ANNOTATIONS)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_ATTRIBUTES)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_SYMFONY)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_PHP_STANDALONE)
        ) {
            return NullViolation::create();
        }

        $indention = $line->indention();

        $lines->next();

        while ($lines->valid()
            && !$lines->current()->isDirective()
            && ($indention < $lines->current()->indention() || $lines->current()->isBlank())
        ) {
            if ($matches = $lines->current()->clean()->match('/^use (.*) (AS|As|aS) (.*);$/')) {
                $message = sprintf('Please use lowercase "as" instead of "%s"', $matches[2]);

                return Violation::from(
                    $message,
                    $filename,
                    $number + 1,
                    ''
                );
            }

            $lines->next();
        }

        return NullViolation::create();
    }
}
