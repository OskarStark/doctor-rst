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
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Use `$this->assert*` over static calls.')]
#[InvalidExample('self::assertTrue($foo);')]
#[ValidExample('$this->assertTrue($foo);')]
final class NonStaticPhpunitAssertions extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);

        if (!$this->inPhpCodeBlock($lines, $number)) {
            return NullViolation::create();
        }

        $lines->next();

        if ($lines->current()->isBlank()) {
            $lines->next();
        }

        if ($lines->current()->raw()->match('/self::assert*/')
            || $lines->current()->raw()->match('/static::assert*/')
        ) {
            return Violation::from(
                'Please use `$this->assert*` over static call',
                $filename,
                $number + 1,
                $lines->current()->clean()->toString(),
            );
        }

        return NullViolation::create();
    }
}
