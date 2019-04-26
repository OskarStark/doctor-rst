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

class Typo extends CheckListRule
{
    public static function getGroups(): array
    {
        return [Registry::GROUP_SONATA, Registry::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match($this->pattern, $line, $matches)) {
            return sprintf($this->message, $matches[0]);
        }
    }

    public function getDefaultMessage(): string
    {
        return 'Typo in word "%s"';
    }

    public static function getList(): array
    {
        return [
            '/compsoer/i' => null,
            '/registerbundles\(\)/' => 'Typo in word "%s", use "registerBundles()"',
            '/retun/' => null,
            '/displayes/i' => null,
            '/mantains/i' => null,
            '/doctine/i' => null,
            '/adress/i' => null,
            '/argon21/' => 'Typo in word "%s", use "argon2i"',
            '/descritpion/i' => null,
            '/recalcuate/i' => null,
        ];
    }
}
