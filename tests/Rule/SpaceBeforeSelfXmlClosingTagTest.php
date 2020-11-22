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

use App\Rule\SpaceBeforeSelfXmlClosingTag;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class SpaceBeforeSelfXmlClosingTagTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new SpaceBeforeSelfXmlClosingTag())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please add space before "/>"',
                new RstSample('<argument type="service" id="sonata.admin.search.handler"/>'),
            ],
            [
                'Please add space before "/>"',
                new RstSample('<argument/>'),
            ],
            [
                null,
                new RstSample(' />'),
            ],
            [
                null,
                new RstSample('<argument type="service" id="sonata.admin.search.handler" />'),
            ],
            [
                null,
                new RstSample('<br />'),
            ],
        ];
    }
}
