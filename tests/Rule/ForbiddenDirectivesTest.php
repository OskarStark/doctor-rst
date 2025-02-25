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

use App\Rule\ForbiddenDirectives;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class ForbiddenDirectivesTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(array $directiveOptions, ViolationInterface $expected, RstSample $sample): void
    {
        $rule = new ForbiddenDirectives();
        $rule->setOptions([
            'directives' => $directiveOptions,
        ]);

        self::assertEquals(
            $expected,
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: array, 1: ViolationInterface, 2: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            [
                '.. index::',
            ],
            Violation::from(
                'Please don\'t use directive ".. index::" anymore',
                'filename',
                1,
                '.. index::',
            ),
            new RstSample([
                '.. index::',
            ]),
        ];

        yield [
            [
                [
                    'directive' => '.. notice::',
                ],
            ],
            Violation::from(
                'Please don\'t use directive ".. notice::" anymore',
                'filename',
                1,
                '.. notice::',
            ),
            new RstSample([
                '.. notice::',
            ]),
        ];

        yield [
            [
                [
                    'directive' => '.. caution::',
                    'replacements' => '.. warning::',
                ],
            ],
            Violation::from(
                'Please don\'t use directive ".. caution::" anymore, use ".. warning::" instead',
                'filename',
                1,
                '.. caution::',
            ),
            new RstSample([
                '.. caution::',
            ]),
        ];

        yield [
            [
                [
                    'directive' => '.. caution::',
                    'replacements' => ['.. warning::', '.. danger::'],
                ],
            ],
            Violation::from(
                'Please don\'t use directive ".. caution::" anymore, use ".. warning::" or ".. danger::" instead',
                'filename',
                1,
                '.. caution::',
            ),
            new RstSample([
                '.. caution::',
            ]),
        ];

        yield [
            [
                '.. index::',
            ],
            NullViolation::create(),
            new RstSample([
                '.. tip::',
            ]),
        ];

        yield [
            [
                '.. index::',
            ],
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }

    #[Test]
    public function invalidOptionType(): void
    {
        $this->expectExceptionObject(
            new InvalidOptionsException('The option "directives" with value ".. caution::" is expected to be of type "array", but is of type "string".'),
        );

        $rule = new ForbiddenDirectives();
        $rule->setOptions([
            'directives' => '.. caution::',
        ]);
    }

    #[Test]
    public function invalidDirective(): void
    {
        $this->expectExceptionObject(
            new InvalidOptionsException('A directive in "directives" is invalid. It needs at least a "directive" key with a string value'),
        );

        $rule = new ForbiddenDirectives();
        $rule->setOptions([
            'directives' => [
                [
                    'directive' => 2,
                    'replacements' => '.. caution::',
                ],
            ],
        ]);
    }

    #[Test]
    public function missingDirective(): void
    {
        $this->expectExceptionObject(
            new InvalidOptionsException('A directive in "directives" is invalid. It needs at least a "directive" key with a string value'),
        );

        $rule = new ForbiddenDirectives();
        $rule->setOptions([
            'directives' => [
                [
                    'replacements' => '.. caution::',
                ],
            ],
        ]);
    }

    #[Test]
    public function checkWithNoConfiguration(): void
    {
        $rule = new ForbiddenDirectives();
        $rule->setOptions([]);

        $sample = new RstSample('temp');
        self::assertEquals(
            NullViolation::create(),
            $rule->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }
}
