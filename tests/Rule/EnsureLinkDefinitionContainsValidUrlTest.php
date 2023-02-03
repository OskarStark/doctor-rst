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

use App\Rule\EnsureLinkDefinitionContainsValidUrl;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class EnsureLinkDefinitionContainsValidUrlTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new EnsureLinkDefinitionContainsValidUrl())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return \Generator<string, array{0: ViolationInterface, 1: RstSample}>
     */
    public function validProvider(): \Generator
    {
        $validCases = [
            '.. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST',
            '.. _`DOCtor-RST`: https://github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`: https://github.com/OskarStark/DOCtor-RST',
            '.. _DOCtor-RST: http://github.com/OskarStark/DOCtor-RST',
            '.. _`DOCtor-RST`: http://github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`: http://github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`: http://google.com',
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
    public function invalidProvider(): \Generator
    {
        $invalidCases = [
            '.. _DOCtor-RST: ttp://github.com/OskarStark/DOCtor-RST',
            '.. _`DOCtor-RST`: http:/github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`: http//github.com/OskarStark/DOCtor-RST',
            '.. _`use DOCtor-RST`: https//github.com/OskarStark/DOCtor-RST',
        ];

        foreach ($invalidCases as $invalidCase) {
            yield $invalidCase => [
                Violation::from(
                    sprintf(
                        'Invalid url in "%s"',
                        $invalidCase
                    ),
                    'filename',
                    1,
                    $invalidCase
                ),
                new RstSample($invalidCase),
            ];
        }
    }
}
