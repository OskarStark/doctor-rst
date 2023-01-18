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

final class SpaceBeforeSelfXmlClosingTagTest extends \App\Tests\UnitTestCase
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

    /**
     * @return array<array{0: null|string, 1: RstSample}>
     */
    public function checkProvider(): array
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
            [
                null,
                new RstSample('`Twig docs <https://twig.symfony.com/doc/2.x/>`_;'),
            ],
        ];
    }
}
