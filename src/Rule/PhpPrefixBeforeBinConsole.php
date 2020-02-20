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
 * @Description("Ensure `bin/console` is prefixed with `php` to be safe executable on Microsoft Windows.")
 * @InvalidExample("bin/console list")
 * @ValidExample("php bin/console list")
 */
class PhpPrefixBeforeBinConsole extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_SYMFONY)];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!preg_match('/bin\/console/', $line->raw())) {
            return null;
        }

        if (preg_match('/(`|"|_|├─ )bin\/console/u', $line->raw())
            || preg_match('/php "%s\/\.\.\/bin\/console"/', $line->raw())) {
            return null;
        }

        if (RstParser::isLinkDefinition($line)) {
            return null;
        }

        if (!preg_match('/php(.*)bin\/console/', $line->raw())) {
            return 'Please add "php" prefix before "bin/console"';
        }

        return null;
    }
}
