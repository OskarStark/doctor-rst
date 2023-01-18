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

final class EnsureLinkDefinitionContainsValidUrlTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new EnsureLinkDefinitionContainsValidUrl())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<string, array{0: null, 1: RstSample}>
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
                null,
                new RstSample($validCase),
            ];
        }
    }

    /**
     * @return \Generator<string, array{0: string, 1: RstSample}>
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
                sprintf(
                    'Invalid url in "%s"',
                    $invalidCase
                ),
                new RstSample($invalidCase),
            ];
        }
    }
}
