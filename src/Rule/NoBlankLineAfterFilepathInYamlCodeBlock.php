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
use App\Helper\YamlHelper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

class NoBlankLineAfterFilepathInYamlCodeBlock extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_SYMFONY)];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines = $lines->toIterator();

        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YAML)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YML)
        ) {
            return null;
        }

        $lines->next();
        $lines->next();

        // YML / YAML
        if (preg_match('/^#(.*)\.(yml|yaml)$/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }

        return null;
    }

    private function validateBlankLine(\ArrayIterator $lines, array $matches): ?string
    {
        $lines->next();

        if (RstParser::isBlankLine($lines->current())) {
            $lines->next();
            if (!YamlHelper::isComment($lines->current())) {
                return sprintf('Please remove blank line after "%s"', trim($matches[0]));
            }
        }

        return null;
    }
}
