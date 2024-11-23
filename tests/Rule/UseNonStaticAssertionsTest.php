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

use App\Rule\UseNonStaticAssertions;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class UseNonStaticAssertionsTest extends UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new UseNonStaticAssertions())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use `$this->assert` over static call',
                    'filename',
                    1,
                    'static::assertFalse($expirationChecker->isExpired($validUntil));',
                ),
                new RstSample(
                    'static::assertFalse($expirationChecker->isExpired($validUntil));'
                    , 0),
            ],
            [
                Violation::from(
                    'Please use `$this->assert` over static call',
                    'filename',
                    1,
                    'self::assertSame(\'https://example.com/api/article\', $mockResponse->getRequestUrl());',
                ),
                new RstSample(
                    'self::assertSame(\'https://example.com/api/article\', $mockResponse->getRequestUrl());',
                    0),
            ],
            [
                NullViolation::create(),
                new RstSample('You can use this assertion'),
            ]
        ];
    }
}
