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
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Make sure Composer `--dev` option for `require` command is used at the end.")
 * @InvalidExample("composer require --dev symfony/var-dumper")
 * @ValidExample("composer require symfony/var-dumper --dev")
 */
class ComposerDevOptionAtTheEnd extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/composer require \-\-dev(.*)$/')) {
            return 'Please move "--dev" option to the end of the command';
        }

        return null;
    }
}
