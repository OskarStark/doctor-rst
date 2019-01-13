<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use App\Handler\RulesHandler;

class Replacement implements Rule
{
    public static function getName(): string
    {
        return 'replacement';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (strstr($line, $replacement = '//...')) {
            return sprintf('Please replace "%s" with "// ..."', $replacement);
        }

        if (strstr($line, $replacement = '#...')) {
            return sprintf('Please replace "%s" with "# ..."', $replacement);
        }
    }
}
