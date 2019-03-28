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

use App\Rule\Replacement;
use App\Rule\Rule;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class ReplacementTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $configuredRules = [];
        foreach (Replacement::getList() as $search => $message) {
            $configuredRules[] = (new Replacement())->configure($search, $message);
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
        yield [null, new RstSample('http://...')];
        yield [null, new RstSample('transport://..')];
        yield [null, new RstSample('// ...')];
        yield [null, new RstSample('    // ...')];
        yield [null, new RstSample('# ...')];
        yield [null, new RstSample('    # ...')];
        yield [null, new RstSample('<!-- ... -->')];
        yield [null, new RstSample('    <!-- ... -->')];
        yield [null, new RstSample('{# ... #}')];
        yield [null, new RstSample('    {# ... #}')];

        yield [null, new RstSample('Applications')];
        yield [null, new RstSample('    Applications')];
        yield [null, new RstSample('applications')];
        yield [null, new RstSample('    applications')];

        // todo this should be supported by the regex
        //yield [null, new RstSample('# username is your full Gmail or Google Apps email address')];

        $invalidCases = [
            [
                'Please replace "// .." with "// ..."',
                new RstSample('// ..'),
            ],
            [
                'Please replace "// .." with "// ..."',
                new RstSample('    // ..'),
            ],
            [
                'Please replace "# .." with "# ..."',
                new RstSample('# ..'),
            ],
            [
                'Please replace "# .." with "# ..."',
                new RstSample('    # ..'),
            ],
            [
                'Please replace "<!-- .. -->" with "<!-- ... -->"',
                new RstSample('<!-- .. -->'),
            ],
            [
                'Please replace "<!-- .. -->" with "<!-- ... -->"',
                new RstSample('    <!-- .. -->'),
            ],
            [
                'Please replace "{# .. #}" with "{# ... #}"',
                new RstSample('{# .. #}'),
            ],
            [
                'Please replace "{# .. #}" with "{# ... #}"',
                new RstSample('    {# .. #}'),
            ],
            [
                'Please replace "//.." with "// ..."',
                new RstSample('//..'),
            ],
            [
                'Please replace "//.." with "// ..."',
                new RstSample('    //..'),
            ],
            [
                'Please replace "#.." with "# ..."',
                new RstSample('#..'),
            ],
            [
                'Please replace "#.." with "# ..."',
                new RstSample('    #..'),
            ],
            [
                'Please replace "<!--..-->" with "<!-- ... -->"',
                new RstSample('<!--..-->'),
            ],
            [
                'Please replace "<!--..-->" with "<!-- ... -->"',
                new RstSample('    <!--..-->'),
            ],
            [
                'Please replace "{#..#}" with "{# ... #}"',
                new RstSample('{#..#}'),
            ],
            [
                'Please replace "{#..#}" with "{# ... #}"',
                new RstSample('    {#..#}'),
            ],
            [
                'Please replace "Apps" with "Applications"',
                new RstSample('Apps'),
            ],
            [
                'Please replace "Apps" with "Applications"',
                new RstSample('    Apps'),
            ],
            [
                'Please replace "apps" with "applications"',
                new RstSample('apps'),
            ],
            [
                'Please replace "apps" with "applications"',
                new RstSample('    apps'),
            ],
        ];

        foreach ($invalidCases as $case) {
            yield $case;
        }
    }
}
