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

use App\Rule\Sonata\FinalAdminExtensionClasses;
use PHPUnit\Framework\TestCase;

class FinalAdminExtensionClassesTest extends TestCase
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
            (new FinalAdminExtensionClasses())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use "final" for AdminExtension class',
                'class TestExtension extends AbstractAdminExtension',
            ],
            [
                'Please use "final" for AdminExtension class',
                '    class TestExtension extends AbstractAdminExtension',
            ],
            [
                null,
                'final class TestExtension extends AbstractAdminExtension',
            ],
        ];
    }
}
