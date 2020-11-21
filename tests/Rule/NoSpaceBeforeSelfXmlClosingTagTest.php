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
use PHPUnit\Framework\TestCase;

final class NoSpaceBeforeSelfXmlClosingTagTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new NoSpaceBeforeSelfXmlClosingTag())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove space before "/>"',
                new RstSample('<argument type="service" id="sonata.admin.search.handler" />'),
            ],
            [
                'Please remove space before "/>"',
                new RstSample('<argument />'),
            ],
            [
                null,
                new RstSample('/>'),
            ],
            [
                null,
                new RstSample('<argument type="service" id="sonata.admin.search.handler"/>'),
            ],
            [
                null,
                new RstSample('<br/>'),
            ],
        ];
    }
}
