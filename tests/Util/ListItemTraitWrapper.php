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

use App\Traits\ListTrait;

/**
 * @internal
 */
final class ListItemTraitWrapper
{
    use ListTrait {
        ListTrait::isPartOfListItem as public;
        ListTrait::isPartOfFootnote as public;
        ListTrait::isPartOfRstComment as public;
        ListTrait::isPartOfLineNumberAnnotation as public;
    }
}
