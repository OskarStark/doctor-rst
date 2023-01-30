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

use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class NoAdminYaml extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Sonata()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current()->raw();

        if ($line->match('/_admin\.yaml/')) {
            return NullViolation::create();
        }

        if ($line->match('/admin\.yml/')) {
            $message = 'Please use "services.yaml" instead of "admin.yml"';

            return Violation::from(
                $message,
                $filename,
                1,
                ''
            );
        }

        if ($line->match('/admin\.yaml/')) {
            $message = 'Please use "services.yaml" instead of "admin.yaml"';

            return Violation::from(
                $message,
                $filename,
                1,
                ''
            );
        }

        return NullViolation::create();
    }
}
