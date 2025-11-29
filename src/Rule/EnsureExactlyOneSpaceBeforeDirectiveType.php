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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Ensure exactly one space before directive type.')]
#[InvalidExample('..  code-block:: php')]
#[ValidExample('.. code-block:: php')]
final class EnsureExactlyOneSpaceBeforeDirectiveType extends AbstractRule implements LineContentRule
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

        if (!$line->clean()->match('/\.\.\s*[a-z\-]+::/')) {
            return NullViolation::create();
        }

        if (!$line->clean()->match('/\.\.\ [a-z\-]+::/')) {
            return Violation::from(
                'Please use only one whitespace between ".." and the directive type.',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
