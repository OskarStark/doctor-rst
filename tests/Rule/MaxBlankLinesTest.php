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

use App\Rule\MaxBlankLines;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class MaxBlankLinesTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, int $max, RstSample $sample): void
    {
        $rule = (new MaxBlankLines());
        $rule->setOptions(['max' => $max]);

        self::assertEquals($expected, $rule->check($sample->lines, $sample->lineNumber, 'filename'));
    }

    public static function checkProvider(): iterable
    {
        yield [NullViolation::create(), 2, new RstSample('')];
        yield [NullViolation::create(), 2, new RstSample([
            '',
            '',
        ])];
        yield [
            Violation::from(
                'Please use max 2 blank lines, you used 3',
                'filename',
                1,
                '',
            ),
            2,
            new RstSample([
                '',
                '',
                '',
            ]),
        ];
        yield [
            Violation::from(
                'Please use max 1 blank lines, you used 2',
                'filename',
                1,
                '',
            ),
            1,
            new RstSample([
                '',
                '',
            ]),
        ];

        $invalid = <<<'RST'
Routing is a system for mapping the URL of incoming requests to the controller
function that should be called to process the request. It both allows you
to specify beautiful URLs and keeps the functionality of your application
decoupled from those URLs. Routing is a bidirectional mechanism, meaning that it
should also be used to generate URLs.

Keep Going!
-----------



Routing, check! Now, uncover the power of :doc:`controllers </controller>`.

Learn more about Routing
------------------------
RST;

        yield [
            Violation::from(
                'Please use max 2 blank lines, you used 3',
                'filename',
                9,
                '',
            ),
            2,
            new RstSample($invalid, 8),
        ];
    }
}
