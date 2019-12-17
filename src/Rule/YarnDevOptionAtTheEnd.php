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
use App\Handler\Registry;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Make sure yarn `--dev` option for `add` command is used at the end.")
 * @InvalidExample("yarn add --dev jquery")
 * @ValidExample("yarn add jquery --dev")
 */
class YarnDevOptionAtTheEnd extends AbstractRule implements Rule
{
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

        if (preg_match('/yarn add \-\-dev(.*)$/', RstParser::clean($line))) {
            return 'Please move "--dev" option to the end of the command';
        }

        return null;
    }
}
