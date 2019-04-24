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

use App\Rule\AmericanEnglish;
use App\Rule\Rule;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class AmericanEnglishTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $configuredRules = [];
        foreach (AmericanEnglish::getList() as $search => $message) {
            $configuredRules[] = (new AmericanEnglish())->configure($search, $message);
        }

        $violations = [];
        /** @var Rule $rule */
        foreach ($configuredRules as $rule) {
            $violation = $rule->check($sample->getContent(), $sample->getLineNumber());
            if (null !== $violation) {
                $violations[] = $violation;
            }
        }

        if (null === $expected) {
            $this->assertCount(0, $violations);
        } else {
            $this->assertCount(1, $violations);
            $this->assertSame($expected, $violations[0]);
        }
    }

    public function checkProvider()
    {
        yield [null, new RstSample('behavior')];
        yield [null, new RstSample('Behavior')];
        yield [null, new RstSample('behaviors')];
        yield [null, new RstSample('Behaviors')];

        yield [
            'Please use American English for: behaviour',
            new RstSample('behaviour'),
        ];
        yield [
            'Please use American English for: Behaviour',
            new RstSample('Behaviour'),
        ];
        yield [
            'Please use American English for: behaviours',
            new RstSample('behaviours'),
        ];
        yield [
            'Please use American English for: Behaviours',
            new RstSample('Behaviours'),
        ];

        yield [null, new RstSample('initialize')];
        yield [null, new RstSample('Initialize')];

        yield [
            'Please use American English for: initialise',
            new RstSample('initialise'),
        ];
        yield [
            'Please use American English for: Initialise',
            new RstSample('Initialise'),
        ];
    }
}
