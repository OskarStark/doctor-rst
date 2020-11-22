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

use App\Rule\BlankLineAfterFilepathInYamlCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class BlankLineAfterFilepathInYamlCodeBlockTest extends TestCase
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
            (new BlankLineAfterFilepathInYamlCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            'Please add a blank line after "# config/services.yml"',
            new RstSample([
                '.. code-block:: yml',
                '',
                '    # config/services.yml',
                '    services:',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: yml',
                '',
                '    # config/services.yml',
                '',
                '    services:',
            ]),
        ];
        yield[
            'Please add a blank line after "# config/services.yaml"',
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # config/services.yaml',
                '    services:',
            ]),
        ];
        yield[
            null,
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # config/services.yaml',
                '',
                '    services:',
            ]),
        ];
        yield[
            null,
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # config/services.yaml',
                '    # a comment',
                '    services:',
            ]),
        ];
        yield[
            null,
            new RstSample('temp'),
        ];
    }
}
