<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use App\Attribute\Rule\Description;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure `AbstractAdmin` and the corresponding namespace `Sonata\\AdminBundle\\Admin\\AbstractAdmin` is used.')]
class ExtendAbstractAdmin extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/^class(.*)extends Admin$/')) {
            return Violation::from(
                'Please extend AbstractAdmin instead of Admin',
                $filename,
                $number + 1,
                $line,
            );
        }

        if ($line->clean()->match('/^use Sonata\\\\AdminBundle\\\\Admin\\\\Admin;/')) {
            return Violation::from(
                'Please use "Sonata\AdminBundle\Admin\AbstractAdmin" instead of "Sonata\AdminBundle\Admin\Admin"',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
