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

use App\Handler\Registry;
use App\Value\Lines;
use App\Value\RuleGroup;

class NoAdminYaml extends AbstractRule implements Rule
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

        if (preg_match('/_admin\.yaml/', $line)) {
            return null;
        }

        if (preg_match('/admin\.yml/', $line)) {
            return 'Please use "services.yaml" instead of "admin.yml"';
        }

        if (preg_match('/admin\.yaml/', $line)) {
            return 'Please use "services.yaml" instead of "admin.yaml"';
        }

        return null;
    }
}
