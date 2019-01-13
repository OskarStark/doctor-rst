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

class NoConfigYaml implements Rule
{
    public static function getName(): string
    {
        return 'no_config_yaml';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (strstr(strtolower($line), 'app/config/config.yml')) {
            return 'Please use specific config class in "config/packages/..." instead of "app/config/config.yml"';
        }
    }
}
