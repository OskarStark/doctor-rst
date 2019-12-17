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
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\RuleGroup;

class NoInheritdocInCodeExamples extends AbstractRule implements Rule
{
    use DirectiveTrait;

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

        if (preg_match('/@inheritdoc/', $line)
            && $this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [RstParser::CODE_BLOCK_PHP, RstParser::CODE_BLOCK_PHP_ANNOTATIONS])
        ) {
            return 'Please do not use "@inheritdoc"';
        }

        return null;
    }
}
