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

use App\Rule\NoBlankLineAfterFilepathInYamlCodeBlock;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoBlankLineAfterFilepathInYamlCodeBlockTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new NoBlankLineAfterFilepathInYamlCodeBlock())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please remove blank line after "# config/services.yml"',
                    'filename',
                    1,
                    ''
                ),
                new RstSample([
                    '.. code-block:: yml',
                    '',
                    '    # config/services.yml',
                    '',
                    '    services:',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: yml',
                    '',
                    '    # config/services.yml',
                    '    services:',
                ]),
            ],
            [
                Violation::from(
                    'Please remove blank line after "# config/services.yaml"',
                    'filename',
                    1,
                    ''
                ),
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '',
                    '    services:',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '',
                    '    # a comment',
                    '    services:',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample([
                    '.. code-block:: yaml',
                    '',
                    '    # config/services.yaml',
                    '    services:',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample('temp'),
            ],
        ];
    }
}
