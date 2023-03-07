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

use App\Rule\SectionUnderlineAdornmentMustMatch;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class SectionUnderlineAdornmentMustMatchTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, int $max, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new SectionUnderlineAdornmentMustMatch())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    sprintf('Please ensure title "%s" and underline length are matching', 'Title with too short underline'),
                    'filename',
                    1,
                    'Title with too short underline'
                ),
                new RstSample([
                    'Title with too short underline',
                    '~~~~~~~~~~~~~~~~~~~~~~~~',
                ], 2),
            ],
            [
                Violation::from(
                    sprintf('Please ensure title "%s" and underline length are matching', 'Title with too long underline'),
                    'filename',
                    1,
                    'Title with too long underline'
                ),
                new RstSample([
                    'Title with too long underline',
                    '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~',
                ], 2),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    'Title with matching underline',
                    '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~',
                ], 2),
            ],
        ];
    }
}
