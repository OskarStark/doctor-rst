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

use App\Rule\AmericanEnglish;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class AmericanEnglishTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        $configuredRules = [];

        foreach (AmericanEnglish::getList() as $search => $message) {
            $configuredRules[] = (new AmericanEnglish())->configure($search, $message);
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

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        $valids = [
            'behavior',
            'behaviors',
            'initialize',
            'normalize',
            'organize',
            'recognize',
            'center',
            'color',
            'flavor',
            'license',
        ];

        foreach ($valids as $valid) {
            yield $valid => [NullViolation::create(), new RstSample($valid)];

            $validUppercase = ucfirst($valid);
            yield $validUppercase => [NullViolation::create(), new RstSample($validUppercase)];
        }

        $invalids = [
            'behaviour',
            'behaviours',
            'initialise',
            'normalise',
            'organise',
            'recognise',
            'centre',
            'colour',
            'flavour',
            'licence',
        ];

        foreach ($invalids as $invalid) {
            yield $invalid => [
                Violation::from(
                    \sprintf('Please use American English for: %s', $invalid),
                    'filename',
                    1,
                    $invalid,
                ),
                new RstSample($invalid),
            ];

            $invalidUppercase = ucfirst($invalid);
            yield $invalidUppercase => [
                Violation::from(
                    \sprintf('Please use American English for: %s', $invalidUppercase),
                    'filename',
                    1,
                    $invalidUppercase,
                ),
                new RstSample($invalidUppercase),
            ];
        }
    }
}
