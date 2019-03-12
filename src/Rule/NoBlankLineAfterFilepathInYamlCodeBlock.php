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

use App\Handler\RulesHandler;
use App\Helper\YamlHelper;
use App\Rst\RstParser;

class NoBlankLineAfterFilepathInYamlCodeBlock implements Rule
{
    public static function getName(): string
    {
        return 'no_blank_line_after_filepath_in_yaml_code_block';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YAML)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YML)
        ) {
            return;
        }

        $lines->next();
        $lines->next();

        // YML / YAML
        if (preg_match('/^#(.*)\.(yml|yaml)$/', RstParser::clean($lines->current()), $matches)) {
            return $this->validateBlankLine($lines, $matches);
        }
    }

    private function validateBlankLine(\ArrayIterator $lines, array $matches)
    {
        $lines->next();

        if (RstParser::isBlankLine($lines->current())) {
            $lines->next();
            if (!YamlHelper::isComment($lines->current())) {
                return sprintf('Please remove blank line after "%s"', trim($matches[0]));
            }
        }
    }
}
