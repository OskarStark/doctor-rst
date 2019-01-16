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

namespace app\tests\Rule\Sonata;

use App\Rule\Sonata\NoBashPrompt;
use PHPUnit\Framework\TestCase;

class NoBashPromptTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, $line)
    {
        $this->assertSame(
            $expected,
            (new NoBashPrompt())->check(new \ArrayIterator(\is_array($line) ? $line : [$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove the "$" prefix in .. code-block:: directive',
                [
                    '.. code-block:: bash',
                    '',
                    '$ composer install sonata-project/admin-bundle',
                ],
            ],
            [
                'Please remove the "$" prefix in .. code-block:: directive',
                [
                    '.. code-block:: shell',
                    '',
                    '$ composer install sonata-project/admin-bundle',
                ],
            ],
            [
                null,
                '$ composer install sonata-project/admin-bundle',
            ],
            [
                null,
                'composer install sonata-project/admin-bundle',
            ],
        ];
    }
}
