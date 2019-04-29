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

use App\Rule\Indention;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class IndentionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, int $size, RstSample $sample)
    {
        $rule = (new Indention());
        $rule->setOptions(['size' => $size]);

        $this->assertSame($expected, $rule->check($sample->getContent(), $sample->getLineNumber()));
    }

    public function checkProvider(): \Generator
    {
        yield [null, 4, new RstSample('')];
        yield [
            null,
            4,
            new RstSample(<<<CONTENT
Headline

    Content
CONTENT
            ),
        ];

        yield [
            null,
            4,
            new RstSample(<<<CONTENT
Headline

CONTENT
            ),
        ];

        yield [
            'A file should start without any indention.',
            4,
            new RstSample(<<<CONTENT
  Headline
CONTENT
            ),
        ];

        yield 'wrong without blank line' => [
            'Please add 4 spaces for every indention.',
            4,
            new RstSample(<<<CONTENT
Headline
========
  Content
CONTENT
            ),
        ];

        yield 'wrong with blank line' => [
            'Please add 4 spaces for every indention.',
            4,
            new RstSample(<<<CONTENT
Headline
========

  Content
CONTENT
            ),
        ];
    }
}
