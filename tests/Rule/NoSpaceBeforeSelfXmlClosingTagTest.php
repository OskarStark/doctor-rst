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

use App\Rule\NoSpaceBeforeSelfXmlClosingTag;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoSpaceBeforeSelfXmlClosingTagTest extends \App\Tests\UnitTestCase
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
            (new NoSpaceBeforeSelfXmlClosingTag())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please remove space before "/>"',
                    'filename',
                    1,
                    ''
                ),
                new RstSample('<argument type="service" id="sonata.admin.search.handler" />'),
            ],
            [
                Violation::from(
                    'Please remove space before "/>"',
                    'filename',
                    1,
                    ''
                ),
                new RstSample('<argument />'),
            ],
            [
                NullViolation::create(),
                new RstSample('/>'),
            ],
            [
                NullViolation::create(),
                new RstSample('<argument type="service" id="sonata.admin.search.handler"/>'),
            ],
            [
                NullViolation::create(),
                new RstSample('<br/>'),
            ],
        ];
    }
}
