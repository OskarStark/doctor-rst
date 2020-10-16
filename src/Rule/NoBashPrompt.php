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

use App\Annotations\Rule\Description;
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure no bash prompt `$` is used before commands in `bash`, `shell` or `terminal` code blocks.")
 * @InvalidExample("$ bin/console list")
 * @ValidExample("bin/console list")
 */
class NoBashPrompt extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_BASH)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_SHELL)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TERMINAL)
        ) {
            return null;
        }

        $lines->next();
        $lines->next();

        if ($lines->current()->clean()->match('/^\$ /')) {
            return 'Please remove the "$" prefix in .. code-block:: directive';
        }

        return null;
    }
}
