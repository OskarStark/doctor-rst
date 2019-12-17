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
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure `bin/console` is not prefixed with `php`.")
 * @InvalidExample("php bin/console list")
 * @ValidExample("bin/console list")
 */
class NoPhpPrefixBeforeBinConsole extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_SONATA)];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines = $lines->toIterator();

        $lines->seek($number);
        $line = $lines->current();

        if (preg_match('/php bin\/console/', $line)) {
            return 'Please remove "php" prefix before "bin/console"';
        }

        return null;
    }
}
