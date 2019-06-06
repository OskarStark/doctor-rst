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

use App\Rule\ExtensionXlfInsteadOfXliff;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ExtensionXlfInsteadOfXliffTest extends TestCase
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
            (new ExtensionXlfInsteadOfXliff())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use ".xlf" extension instead of ".xliff"',
                new RstSample('messages.xliff'),
            ],
            [
                null,
                new RstSample('messages.xlf'),
            ],
        ];
    }
}
