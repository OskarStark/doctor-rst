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

namespace app\tests\Rule;

use App\Rule\BlankLineAtEndOfFile;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class BlankLineAtEndOfFileTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new BlankLineAtEndOfFile())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        yield [
            'Please add a blank line add the end of the file',
            new RstSample('bar'),
        ];

        yield [
            null,
            new RstSample(<<<CONTENT
bar

CONTENT
            ),
        ];

        yield [
            null,
            new RstSample(<<<CONTENT
bar\n
CONTENT
            ),
        ];
    }
}
