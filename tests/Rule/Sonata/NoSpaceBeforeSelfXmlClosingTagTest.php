<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule\Sonata;

use App\Rule\Sonata\NoSpaceBeforeSelfXmlClosingTag;
use PHPUnit\Framework\TestCase;

class NoSpaceBeforeSelfXmlClosingTagTest extends TestCase
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
            (new NoSpaceBeforeSelfXmlClosingTag())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please remove space before "/>"',
                '<argument type="service" id="sonata.admin.search.handler" />',
            ],
            [
                null,
                '<argument type="service" id="sonata.admin.search.handler"/>',
            ],
        ];
    }
}
