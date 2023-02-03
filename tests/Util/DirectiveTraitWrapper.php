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

namespace App\Tests\Util;

use App\Traits\DirectiveTrait;

/**
 * @internal
 */
final class DirectiveTraitWrapper
{
    use DirectiveTrait {
        DirectiveTrait::in as public;
        DirectiveTrait::inPhpCodeBlock as public;
        DirectiveTrait::previousDirectiveIs as public;
    }
}
