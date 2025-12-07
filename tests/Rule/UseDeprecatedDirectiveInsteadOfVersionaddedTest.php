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

use App\Rule\UseDeprecatedDirectiveInsteadOfVersionadded;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class UseDeprecatedDirectiveInsteadOfVersionaddedTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new UseDeprecatedDirectiveInsteadOfVersionadded())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return iterable<int|string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function validProvider(): iterable
    {
        yield from [
            [
                NullViolation::create(),
                new RstSample([
                    '.. versionadded:: 3.4',
                    '',
                    '    Foo was added in Symfony 3.4.',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. versionadded:: 3.4',
                    '    Foo was added in Symfony 3.4.',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. deprecated:: 3.4',
                    '',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. deprecated:: 3.4',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
            'versionadded directive with deprecated option' => [
                NullViolation::create(),
                new RstSample([
                    '.. versionadded:: 4.3',
                    '    The ``deprecated`` option for service aliases was introduced in Symfony 4.3.',
                ]),
            ],
        ];
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function invalidProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please use ".. deprecated::" instead of ".. versionadded::"',
                    'filename',
                    1,
                    '.. versionadded:: 3.4',
                ),
                new RstSample([
                    '.. versionadded:: 3.4',
                    '',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
            [
                Violation::from(
                    'Please use ".. deprecated::" instead of ".. versionadded::"',
                    'filename',
                    1,
                    '.. versionadded:: 3.4',
                ),
                new RstSample([
                    '.. versionadded:: 3.4',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
        ];
    }
}
