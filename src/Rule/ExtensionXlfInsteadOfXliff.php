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
use App\Value\RuleGroup;

/**
 * @Description("Make sure to only use `.xlf` instead of `.xliff`.")
 * @ValidExample({"messages.xlf"})
 * @InvalidExample({"messages.xliff"})
 */
class ExtensionXlfInsteadOfXliff extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match('/\.xliff/i', $line, $matches)) {
            return sprintf('Please use ".xlf" extension instead of "%s"', $matches[0]);
        }
    }
}
