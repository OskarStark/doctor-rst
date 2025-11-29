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

use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class StringReplacement extends CheckListRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->containsAny($this->search)) {
            return Violation::from(
                \sprintf($this->message, $this->search),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }

    /**
     * @return array<string, string>
     */
    public static function getList(): array
    {
        return [
            '**type**: ``int``' => 'Please replace "%s" with "**type**: ``integer``"',
            '**type**: ``bool``' => 'Please replace "%s" with "**type**: ``boolean``"',
        ];
    }
}
