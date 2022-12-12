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

namespace App\Tests\Fixtures\Rule;

use App\Rule\AbstractRule;
use App\Rule\Rule;
use App\Value\Lines;

class DummyRule extends AbstractRule implements Rule
{
    public function check(Lines $lines, int $number): ?string
    {
        return null;
    }
}
