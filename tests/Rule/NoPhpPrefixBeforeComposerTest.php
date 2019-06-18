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

use App\Rule\NoPhpPrefixBeforeComposer;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoPhpPrefixBeforeComposerTest extends TestCase
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
            (new NoPhpPrefixBeforeComposer())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove "php" prefix',
                new RstSample('please execute php composer'),
            ],
            [
                null,
                new RstSample('please execute composer install'),
            ],
        ];
    }
}
