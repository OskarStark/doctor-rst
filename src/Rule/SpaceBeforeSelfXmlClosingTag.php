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

class SpaceBeforeSelfXmlClosingTag implements Rule
{
    public static function getName(): string
    {
        return 'space_before_self_xml_closing_tag';
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_DEV];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!preg_match('/\/>/', $line)) {
            return;
        }

        if (!preg_match('/\ \/>/', $line)) {
            return 'Please add space before "/>"';
        }
    }
}
