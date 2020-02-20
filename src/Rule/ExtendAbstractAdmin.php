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
use App\Handler\Registry;
use App\Value\Lines;
use App\Value\RuleGroup;
use function Symfony\Component\String\u;

/**
 * @Description("Ensure `AbstractAdmin` and the corresponding namespace `Sonata\AdminBundle\Admin\AbstractAdmin` is used.")
 */
class ExtendAbstractAdmin extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_SONATA)];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (u($line->clean())->match('/^class(.*)extends Admin$/')) {
            return 'Please extend AbstractAdmin instead of Admin';
        }

        if (u($line->clean())->match('/^use Sonata\\\\AdminBundle\\\\Admin\\\\Admin;/')) {
            return 'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"';
        }

        return null;
    }
}
