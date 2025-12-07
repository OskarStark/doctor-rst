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

use App\Rule\SpaceBeforeSelfXmlClosingTag;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SpaceBeforeSelfXmlClosingTagTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new SpaceBeforeSelfXmlClosingTag())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return array<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield from [
            [
                Violation::from(
                    'Please add space before "/>"',
                    'filename',
                    1,
                    '<argument type="service" id="sonata.admin.search.handler"/>',
                ),
                new RstSample('<argument type="service" id="sonata.admin.search.handler"/>'),
            ],
            [
                Violation::from(
                    'Please add space before "/>"',
                    'filename',
                    1,
                    '<argument/>',
                ),
                new RstSample('<argument/>'),
            ],
            [
                NullViolation::create(),
                new RstSample(' />'),
            ],
            [
                NullViolation::create(),
                new RstSample('<argument type="service" id="sonata.admin.search.handler" />'),
            ],
            [
                NullViolation::create(),
                new RstSample('<br />'),
            ],
            [
                NullViolation::create(),
                new RstSample('`Twig docs <https://twig.symfony.com/doc/2.x/>`_;'),
            ],
        ];
    }
}
