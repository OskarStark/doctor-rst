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

namespace app\tests\Rule;

use App\Rule\ShortArraySyntax;
use PHPUnit\Framework\TestCase;

class ShortArraySyntaxTest extends TestCase
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
            (new ShortArraySyntax())->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                'Please use short array syntax',
                '->add(\'foo\', null, array(\'key\' => 1));',
            ],
            [
                null,
                '->add(\'foo\', null, [\'key\' => 1[);',
            ],
            [
                'Please use short array syntax',
                'if (in_array(1, array())) { ',
            ],
            [
                null,
                'if (in_array(1, [])) {',
            ],
            [
                null,
                '$forms = iterator_to_array($forms);',
            ],
            [
                'Please use short array syntax',
                "->add('tags', null, array('label' => 'les tags'), null, array('expanded' => true, 'multiple' => true));",
            ],
            [
                'Please use short array syntax',
                '->assertLength(array(\'max\' => 100))',
            ],
            [
                null,
                'array(3) {',
            ],
            [
                null,
                ' array(3) {',
            ],
            [
                null,
                '      array(3) {',
            ],
            [
                null,
                'array(999) {      ',
            ],
        ];
    }
}
