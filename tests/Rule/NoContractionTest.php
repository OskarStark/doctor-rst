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

use App\Rule\NoContraction;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class NoContractionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        $configuredRules = [];
        foreach (NoContraction::getList() as $search => $message) {
            $configuredRules[] = (new NoContraction())->configure($search, $message);
        }

        $violations = [];
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

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        $valids = [
            // am
            'i am',
            // are
            'you are',
            'we are',
            'they are',
            // is and hase
            'he is',
            'she is',
            'it is',
            // have
            'you have',
            'we have',
            'they have',
            // will
            'i will',
            'you will',
            'he will',
            'she will',
            'it will',
            'we will',
            'they will',
            // had and would
            'i had',
            'you had',
            'he had',
            'she had',
            'it had',
            'we had',
            'they had',
            'i would',
            'you would',
            'he would',
            'she would',
            'it would',
            'we would',
            'they would',
            // not
            'are not',
            'cannot',
            'could not',
            'did not',
            'has not',
            'have not',
            'is not',
            'must not',
            'shall not',
            'should not',
            'was not',
            'were not',
            'will not',
            'would not',
            // valid usages
            "use PHPUnit's",
        ];

        foreach ($valids as $valid) {
            yield $valid => [null, new RstSample($valid)];

            $validUppercase = ucfirst($valid);
            yield $validUppercase => [null, new RstSample($validUppercase)];
        }

        $invalids = [
            // am
            "i'm",
            // are
            "you're",
            "we're",
            "they're",
            // is and hase
            "he's",
            "she's",
            "it's",
            // have
            "you've",
            "we've",
            "they've",
            // will
            "i'll",
            "you'll",
            "he'll",
            "she'll",
            "it'll",
            "we'll",
            "they'll",
            // had and would
            "i'd",
            "you'd",
            "he'd",
            "she'd",
            "it'd",
            "we'd",
            "they'd",
            // not
            "aren't",
            "can't",
            "couldn't",
            "didn't",
            "hasn't",
            "haven't",
            "isn't",
            "mustn't",
            "shan't",
            "shouldn't",
            "wasn't",
            "weren't",
            "won't",
            "wouldn't",
        ];

        foreach ($invalids as $invalid) {
            yield $invalid => [
                sprintf('Please do not use contraction for: %s', $invalid),
                new RstSample($invalid),
            ];

            $invalidUppercase = ucfirst($invalid);
            yield $invalidUppercase => [
                sprintf('Please do not use contraction for: %s', $invalidUppercase),
                new RstSample($invalidUppercase),
            ];
        }
    }
}
