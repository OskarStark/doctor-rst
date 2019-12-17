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

use App\Rule\ComposerDevOptionNotAtTheEnd;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ComposerDevOptionNotAtTheEndTest extends TestCase
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
            (new ComposerDevOptionNotAtTheEnd())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            new RstSample('composer require --dev symfony/debug'),
        ];
        yield[
            'Please move "--dev" option before the package',
            new RstSample('composer require symfony/debug --dev'),
        ];
    }
}
