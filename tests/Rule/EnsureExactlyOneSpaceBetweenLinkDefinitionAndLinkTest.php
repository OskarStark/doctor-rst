<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class EnsureExactlyOneSpaceBetweenLinkDefinitionAndLinkTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider invalidProvider
     * @dataProvider validProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureExactlyOneSpaceBetweenLinkDefinitionAndLink())->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function validProvider(): iterable
    {
        $validCases = [
            '.. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST',
            '.. _`DOCtor-RST`: https://github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`: https://github.com/OskarStark/DOCtor-RST',
        ];

        foreach ($validCases as $validCase) {
            yield $validCase => [
                NullViolation::create(),
                new RstSample($validCase),
            ];
        }
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public static function invalidProvider(): iterable
    {
        $invalidCases = [
            '.. _DOCtor-RST:  https://github.com/OskarStark/DOCtor-RST',
            '.. _`DOCtor-RST`:  https://github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`:  https://github.com/OskarStark/DOCtor-RST',
        ];

        foreach ($invalidCases as $invalidCase) {
            yield $invalidCase => [
                Violation::from(
                    'Please use only one whitespace between the link definition and the link.',
                    'filename',
                    1,
                    $invalidCase,
                ),
                new RstSample($invalidCase),
            ];
        }
    }
}
