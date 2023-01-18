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

use App\Rule\UseHttpsXsdUrls;
use App\Tests\RstSample;

final class UseHttpsXsdUrlsTest extends \App\Tests\UnitTestCase
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
            (new UseHttpsXsdUrls())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        yield [null, new RstSample('')];
        yield [
            null,
            new RstSample('https://symfony.com/schema/dic/services/services-1.0.xsd'),
        ];
        yield [
            'Please use "https" for http://symfony.com/schema/dic/services/services-1.0.xsd',
            new RstSample('http://symfony.com/schema/dic/services/services-1.0.xsd'),
        ];
    }
}
