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

use App\Rule\NonStaticPhpunitAssertions;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NonStaticPhpunitAssertionsTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NonStaticPhpunitAssertions())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample([
                'static::assertFalse($expirationChecker->isExpired($validUntil));',
            ]),
        ];

        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                Violation::from(
                    'Please use `$this->assert*` over static call',
                    'filename',
                    3,
                    'static::assertFalse($expirationChecker->isExpired($validUntil));',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    static::assertFalse($expirationChecker->isExpired($validUntil));',
                ], 2),
            ];

            yield [
                Violation::from(
                    'Please use `$this->assert*` over static call',
                    'filename',
                    3,
                    'self::assertSame(\'https://example.com/api/article\', $mockResponse->getRequestUrl());',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    self::assertSame(\'https://example.com/api/article\', $mockResponse->getRequestUrl());',
                ], 2),
            ];

            yield [
                Violation::from(
                    'Please use `$this->assert*` over static call',
                    'filename',
                    2,
                    'self::assert(true);',
                ),
                new RstSample([
                    $codeBlock,
                    '    self::assert(true);',
                ], 1),
            ];

            yield [
                Violation::from(
                    'Please use `$this->assert*` over static call',
                    'filename',
                    2,
                    'static::assert(true);',
                ),
                new RstSample([
                    $codeBlock,
                    '    static::assert(true);',
                ], 1),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    You can use this assertion',
                ]),
            ];

            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '    $this->assert(true);',
                ]),
            ];
        }
    }
}
