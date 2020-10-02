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

use App\Rule\NoInheritdocInCodeExamples;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoInheritdocInCodeExamplesTest extends TestCase
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
            (new NoInheritdocInCodeExamples())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                new RstSample('* {@inheritdoc}'),
            ],
            [
                null,
                new RstSample('fine'),
            ],
            [
                'Please do not use "@inheritdoc"',
                new RstSample([
                    '.. code-block:: php',
                    '',
                    '    /*',
                    '     * {@inheritdoc}',
                ], 3),
            ],
        ];
    }
}
