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

use App\Rule\NoComposerReq;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoComposerReqTest extends TestCase
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
            (new NoComposerReq())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please "composer require" instead of "composer req"',
                new RstSample('composer req symfony/form'),
            ],
            [
                null,
                new RstSample('composer require symfony/form'),
            ],
        ];
    }
}
