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
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class ShortArraySyntaxTest extends \App\Tests\UnitTestCase
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
            (new ShortArraySyntax())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please use short array syntax',
                    'filename',
                    1,
                    ''
                ),
                new RstSample('->add(\'foo\', null, array(\'key\' => 1));'),
            ],
            [
                NullViolation::create(),
                new RstSample('->add(\'foo\', null, [\'key\' => 1[);'),
            ],
            [
                Violation::from(
                    'Please use short array syntax',
                    'filename',
                    1,
                    ''
                ),
                new RstSample('if (in_array(1, array())) { '),
            ],
            [
                NullViolation::create(),
                new RstSample('if (in_array(1, [])) {'),
            ],
            [
                NullViolation::create(),
                new RstSample('$forms = iterator_to_array($forms);'),
            ],
            [
                Violation::from(
                    'Please use short array syntax',
                    'filename',
                    1,
                    ''
                ),
                new RstSample("->add('tags', null, array('label' => 'les tags'), null, array('expanded' => true, 'multiple' => true));"),
            ],
            [
                Violation::from(
                    'Please use short array syntax',
                    'filename',
                    1,
                    ''
                ),
                new RstSample('->assertLength(array(\'max\' => 100))'),
            ],
            [
                NullViolation::create(),
                new RstSample('array(3) {'),
            ],
            [
                NullViolation::create(),
                new RstSample(' array(3) {'),
            ],
            [
                NullViolation::create(),
                new RstSample('      array(3) {'),
            ],
            [
                NullViolation::create(),
                new RstSample('array(999) {      '),
            ],
        ];
    }
}
