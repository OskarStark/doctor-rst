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

use App\Rule\NoInheritdocInCodeExamples;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoInheritdocInCodeExamplesTest extends \App\Tests\UnitTestCase
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
            (new NoInheritdocInCodeExamples())->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    public static function checkProvider(): \Generator
    {
        yield [
            NullViolation::create(),
            new RstSample('* {@inheritdoc}'),
        ];

        yield [
            NullViolation::create(),
            new RstSample('fine'),
        ];

        foreach (self::phpCodeBlocks() as $codeBlock) {
            yield [
                Violation::from(
                    'Please do not use "@inheritdoc"',
                    'filename',
                    4,
                    '* {@inheritdoc}',
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    /*',
                    '     * {@inheritdoc}',
                ], 3),
            ];
        }
    }
}
