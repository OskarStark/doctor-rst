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
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoBashPromptTest extends \App\Tests\UnitTestCase
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
            (new NoBashPrompt())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please remove the "$" prefix in .. code-block:: directive',
                    'filename',
                    1,
                    '$ composer install sonata-project/admin-bundle'
                ),
                new RstSample([
                    '.. code-block:: bash',
                    '',
                    '    $ composer install sonata-project/admin-bundle',
                ]),
            ],
            [
                Violation::from(
                    'Please remove the "$" prefix in .. code-block:: directive',
                    'filename',
                    1,
                    '$ composer install sonata-project/admin-bundle'
                ),
                new RstSample([
                    '.. code-block:: shell',
                    '',
                    '    $ composer install sonata-project/admin-bundle',
                ]),
            ],
            [
                Violation::from(
                    'Please remove the "$" prefix in .. code-block:: directive',
                    'filename',
                    1,
                    '$ composer install sonata-project/admin-bundle'
                ),
                new RstSample([
                    '.. code-block:: terminal',
                    '',
                    '    $ composer install sonata-project/admin-bundle',
                ]),
            ],
            [
                NullViolation::create(),
                new RstSample('$ composer install sonata-project/admin-bundle'),
            ],
            [
                NullViolation::create(),
                new RstSample('composer install sonata-project/admin-bundle'),
            ],
        ];
    }
}
