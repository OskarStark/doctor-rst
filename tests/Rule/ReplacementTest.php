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

use App\Rule\Replacement;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ReplacementTest extends TestCase
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
            (new Replacement())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        yield [null, new RstSample('http://...')];
        yield [null, new RstSample('transport://..')];
        yield [null, new RstSample('// ...')];
        yield [null, new RstSample('    // ...')];
        yield [null, new RstSample('# ...')];
        yield [null, new RstSample('    # ...')];
        yield [null, new RstSample('<!-- ... -->')];
        yield [null, new RstSample('    <!-- ... -->')];

        $invalidCases = [
            [
                'Please replace "// .." with "// ..."',
                new RstSample('// ..'),
            ],
            [
                'Please replace "// .." with "// ..."',
                new RstSample('    // ..'),
            ],
            [
                'Please replace "# .." with "# ..."',
                new RstSample('# ..'),
            ],
            [
                'Please replace "# .." with "# ..."',
                new RstSample('    # ..'),
            ],
            [
                'Please replace "<!-- .. -->" with "<!-- ... -->"',
                new RstSample('<!-- .. -->'),
            ],
            [
                'Please replace "<!-- .. -->" with "<!-- ... -->"',
                new RstSample('    <!-- .. -->'),
            ],
            [
                'Please replace "//.." with "// ..."',
                new RstSample('//..'),
            ],
            [
                'Please replace "//.." with "// ..."',
                new RstSample('    //..'),
            ],
            [
                'Please replace "#.." with "# ..."',
                new RstSample('#..'),
            ],
            [
                'Please replace "#.." with "# ..."',
                new RstSample('    #..'),
            ],
            [
                'Please replace "<!--..-->" with "<!-- ... -->"',
                new RstSample('<!--..-->'),
            ],
            [
                'Please replace "<!--..-->" with "<!-- ... -->"',
                new RstSample('    <!--..-->'),
            ],
        ];

        foreach ($invalidCases as $case) {
            yield $case;
        }
    }
}
