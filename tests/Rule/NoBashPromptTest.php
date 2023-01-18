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

use App\Rule\NoBashPrompt;
use App\Tests\RstSample;

final class NoBashPromptTest extends \App\Tests\UnitTestCase
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
            (new NoBashPrompt())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                'Please remove the "$" prefix in .. code-block:: directive',
                new RstSample([
                    '.. code-block:: bash',
                    '',
                    '$ composer install sonata-project/admin-bundle',
                ]),
            ],
            [
                'Please remove the "$" prefix in .. code-block:: directive',
                new RstSample([
                    '.. code-block:: shell',
                    '',
                    '$ composer install sonata-project/admin-bundle',
                ]),
            ],
            [
                'Please remove the "$" prefix in .. code-block:: directive',
                new RstSample([
                    '.. code-block:: terminal',
                    '',
                    '$ composer install sonata-project/admin-bundle',
                ]),
            ],
            [
                null,
                new RstSample('$ composer install sonata-project/admin-bundle'),
            ],
            [
                null,
                new RstSample('composer install sonata-project/admin-bundle'),
            ],
        ];
    }
}
