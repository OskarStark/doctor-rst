<?php

declare(strict_types=1);

/*
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\YarnDevOptionNotAtTheEnd;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class YarnDevOptionNotAtTheEndTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new YarnDevOptionNotAtTheEnd())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            NullViolation::create(),
            new RstSample('yarn add --dev jquery'),
        ];

        yield [
            Violation::from(
                'Please move "--dev" option before the package',
                'filename',
                1,
                'yarn add jquery --dev'
            ),
            new RstSample('yarn add jquery --dev'),
        ];
    }
}
