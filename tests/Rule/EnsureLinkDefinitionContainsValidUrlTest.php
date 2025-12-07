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

use App\Rule\EnsureLinkDefinitionContainsValidUrl;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureLinkDefinitionContainsValidUrlTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureLinkDefinitionContainsValidUrl())->check($sample->lines, $sample->lineNumber, 'filename'),
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
    public static function invalidProvider(): iterable
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
                    \sprintf(
                        'Invalid url in "%s"',
                        $invalidCase,
                    ),
                    'filename',
                    1,
                    $invalidCase,
                ),
                new RstSample($invalidCase),
            ];
        }
    }
}
