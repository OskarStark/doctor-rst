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

namespace App\Tests\Rule;

use App\Rule\NoEmptyLiterals;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;

final class NoEmptyLiteralsTest extends AbstractLineContentRuleTestCase
{
    public function createRule(): NoEmptyLiterals
    {
        return new NoEmptyLiterals();
    }

    public static function checkProvider(): iterable
    {
        $invalid = 'Please use ````...';
        $valid = 'Please use ``foo``...';

        yield 'valid' => [NullViolation::create(), new RstSample($valid)];

        yield 'invalid' => [
            Violation::from(
                'Empty literals (````) are not allowed!',
                'filename',
                1,
                $invalid,
            ),
            new RstSample($invalid),
        ];
    }
}
