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
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\RuleGroup;
use function Symfony\Component\String\u;

class NoInheritdocInCodeExamples extends AbstractRule implements Rule
{
    use DirectiveTrait;

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (u($line->raw())->match('/@inheritdoc/')
            && $this->in(
                RstParser::DIRECTIVE_CODE_BLOCK,
                $lines,
                $number,
                [
                    RstParser::CODE_BLOCK_PHP,
                    RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
                    RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
                ]
            )
        ) {
            return 'Please do not use "@inheritdoc"';
        }

        return null;
    }
}
