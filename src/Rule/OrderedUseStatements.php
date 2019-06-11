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
use App\Value\RuleGroup;

class OrderedUseStatements extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
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

        $statements = [];
        $indentionOfFirstFoundUseStatement = null;

        while ($lines->valid()
            && !RstParser::isDirective($lines->current())
            && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))
            && (!preg_match('/^((class|trait) (.*)|\$)/', RstParser::clean($lines->current())))
        ) {
            if (preg_match('/^use (.*);$/', RstParser::clean($lines->current()), $matches)) {
                if (null === $indentionOfFirstFoundUseStatement) {
                    $indentionOfFirstFoundUseStatement = RstParser::indention($lines->current());
                    $statements[] = $this->extractClass(RstParser::clean($lines->current()));
                } else {
                    if ($indentionOfFirstFoundUseStatement != RstParser::indention($lines->current())) {
                        break;
                    }

                    $statements[] = $this->extractClass(RstParser::clean($lines->current()));
                }
            }

            $lines->next();
        }

        if (empty($statements) || 1 === \count($statements)) {
            return;
        }

        $sortedUseStatements = $statements;

        natsort($sortedUseStatements);

        if ($statements !== $sortedUseStatements) {
            return 'Please reorder the use statements alphabetical';
        }
    }

    private function extractClass(string $useStatement): string
    {
        preg_match('/use (.*);/', $useStatement, $matches);

        // the "A" here helps to sort !!
        return strtolower(str_replace('\\', 'A', trim($matches[1])));
    }
}
