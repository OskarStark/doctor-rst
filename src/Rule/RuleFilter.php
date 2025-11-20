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

final class RuleFilter
{
    /**
     * @template T of Rule
     *
     * @param Rule[]          $rules
     * @param class-string<T> $type
     *
     * @return T[]
     */
    public static function byType(array $rules, string $type): array
    {
        return array_filter($rules, static fn (Rule $rule): bool => $rule instanceof $type);
    }
}
