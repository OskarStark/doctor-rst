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

use App\Attribute\Rule\Description;
use App\Rule\StringReplacement;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[Description('propose to replace a string with another string.')]
final class StringReplacementTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        $configuredRules = [];

        foreach (StringReplacement::getList() as $search => $message) {
            $configuredRules[] = (new StringReplacement())->configure($search, $message);
        }

        $violations = [];

        foreach ($configuredRules as $rule) {
            $violation = $rule->check($sample->lines, $sample->lineNumber, 'filename');

            if (!$violation->isNull()) {
                $violations[] = $violation;
            }
        }

        if ($expected->isNull()) {
            self::assertEmpty($violations);
        } else {
            self::assertCount(1, $violations);
            self::assertEquals($expected, $violations[0]);
        }
    }

    public static function checkProvider(): iterable
    {
        yield 'empty string' => [NullViolation::create(), new RstSample('')];

        $valids = [
            '**type**: ``integer``',
            '**type**: ``boolean``',
        ];

        foreach ($valids as $valid) {
            yield $valid => [
                NullViolation::create(),
                new RstSample($valid),
            ];

            // add leading spaces
            yield \sprintf('"%s" with leading spaces', $valid) => [
                NullViolation::create(),
                new RstSample(\sprintf(
                    '    %s',
                    $valid,
                )),
            ];
        }

        $invalids = [
            '**type**: ``int``' => '**type**: ``integer``',
            '**type**: ``bool``' => '**type**: ``boolean``',
        ];

        foreach ($invalids as $invalid => $valid) {
            yield $invalid => [
                Violation::from(
                    \sprintf(
                        'Please replace "%s" with "%s"',
                        $invalid,
                        $valid,
                    ),
                    'filename',
                    1,
                    $invalid,
                ),
                new RstSample($invalid),
            ];

            // add leading spaces
            yield \sprintf('"%s" with leading spaces', $invalid) => [
                Violation::from(
                    \sprintf(
                        'Please replace "%s" with "%s"',
                        $invalid,
                        $valid,
                    ),
                    'filename',
                    1,
                    trim($invalid),
                ),
                new RstSample(\sprintf(
                    '    %s',
                    $invalid,
                )),
            ];
        }
    }
}
