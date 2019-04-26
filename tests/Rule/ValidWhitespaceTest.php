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

use App\Rule\ValidWhitespace;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ValidWhitespaceTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new ValidWhitespace())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        yield [null, new RstSample('')];
        yield [null, new RstSample(' ')];

        yield [
            'no',
            new RstSample(dechex(160)),
        ];
    }
}
