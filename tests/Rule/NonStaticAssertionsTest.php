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

use App\Rule\NonStaticAssertions;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NonStaticAssertionsTest extends UnitTestCase
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
            (new NonStaticAssertions())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        foreach (self::phpCodeBlocks() as $codeBlock) {
        
            yield [
                Violation::from(
                    'Please use `$this->assert` over static call',
                    'filename',
                    1,
                    'static::assertFalse($expirationChecker->isExpired($validUntil));',
                ),
                new RstSample([
                    $codeBlock,
                    'static::assertFalse($expirationChecker->isExpired($validUntil));'
                ], 0),
            ];

            yield [
                Violation::from(
                    'Please use `$this->assert` over static call',
                    'filename',
                    1,
                    'self::assertSame(\'https://example.com/api/article\', $mockResponse->getRequestUrl());',
                ),
                new RstSample([
                    $codeBlock,
                    'self::assertSame(\'https://example.com/api/article\', $mockResponse->getRequestUrl());'
                ], 0),
            ];

            yield [
                Violation::from(
                    'Please use `$this->assert` over static call',
                    'filename',
                    1,
                    'self::assert(true);',
                ),
                new RstSample([
                    $codeBlock,
                    'self::assert(true);'
                ], 0),
            ];

            yield [
                Violation::from(
                    'Please use `$this->assert` over static call',
                    'filename',
                    1,
                    'static::assert(true);',
                ),
                new RstSample([
                    $codeBlock,
                    'static::assert(true);'
                ], 0),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    'You can use this assertion'
                ])
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '$this->assert(true);'
                ])
            ];
        }
    }
}
