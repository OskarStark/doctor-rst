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

use App\Annotations\Rule\Description;
use App\Rule\Rule;
use App\Rule\StringReplacement;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

/**
 * @Description("propose to replace a string with another string.")
 */
final class StringReplacementTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $configuredRules = [];
        foreach (StringReplacement::getList() as $search => $message) {
            $configuredRules[] = (new StringReplacement())->configure($search, $message);
        }

        $violations = [];
        /** @var Rule $rule */
        foreach ($configuredRules as $rule) {
            $violation = $rule->check($sample->lines(), $sample->lineNumber());
            if (null !== $violation) {
                $violations[] = $violation;
            }
        }

        if (null === $expected) {
            static::assertCount(0, $violations);
        } else {
            static::assertCount(1, $violations);
            static::assertSame($expected, $violations[0]);
        }
    }

    public function checkProvider()
    {
        yield 'empty string' => [null, new RstSample('')];

        $valids = [
            '**type**: ``integer``',
            '**type**: ``boolean``',
        ];

        foreach ($valids as $valid) {
            yield $valid => [
                null,
                new RstSample($valid),
            ];

            // add leading spaces
            yield sprintf('"%s" with leading spaces', $valid) => [
                null,
                new RstSample(sprintf(
                    '    %s',
                    $valid
                )),
            ];
        }

        $invalids = [
            '**type**: ``int``' => '**type**: ``integer``',
            '**type**: ``bool``' => '**type**: ``boolean``',
        ];

        foreach ($invalids as $invalid => $valid) {
            yield $invalid => [
                sprintf(
                    'Please replace "%s" with "%s"',
                    $invalid,
                    $valid
                ),
                new RstSample($invalid),
            ];

            // add leading spaces
            yield sprintf('"%s" with leading spaces', $invalid) => [
                sprintf(
                    'Please replace "%s" with "%s"',
                    $invalid,
                    $valid
                ),
                new RstSample(sprintf(
                    '    %s',
                    $invalid
                )),
            ];
        }
    }
}
