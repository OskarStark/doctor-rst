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

use App\Rule\ShortArraySyntax;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class ShortArraySyntaxTest extends TestCase
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
            (new ShortArraySyntax())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return array<array{0: null|string, 1: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                'Please use short array syntax',
                new RstSample('->add(\'foo\', null, array(\'key\' => 1));'),
            ],
            [
                null,
                new RstSample('->add(\'foo\', null, [\'key\' => 1[);'),
            ],
            [
                'Please use short array syntax',
                new RstSample('if (in_array(1, array())) { '),
            ],
            [
                null,
                new RstSample('if (in_array(1, [])) {'),
            ],
            [
                null,
                new RstSample('$forms = iterator_to_array($forms);'),
            ],
            [
                'Please use short array syntax',
                new RstSample("->add('tags', null, array('label' => 'les tags'), null, array('expanded' => true, 'multiple' => true));"),
            ],
            [
                'Please use short array syntax',
                new RstSample('->assertLength(array(\'max\' => 100))'),
            ],
            [
                null,
                new RstSample('array(3) {'),
            ],
            [
                null,
                new RstSample(' array(3) {'),
            ],
            [
                null,
                new RstSample('      array(3) {'),
            ],
            [
                null,
                new RstSample('array(999) {      '),
            ],
        ];
    }
}
