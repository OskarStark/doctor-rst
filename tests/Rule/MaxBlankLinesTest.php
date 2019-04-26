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

use App\Rule\MaxBlankLines;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class MaxBlankLinesTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, int $max, RstSample $sample)
    {
        $rule = (new MaxBlankLines());
        $rule->setOptions(['max' => $max]);

        $this->assertSame($expected, $rule->check($sample->getContent(), $sample->getLineNumber()));
    }

    public function checkProvider(): \Generator
    {
        yield [null, 2, new RstSample('')];
        yield [null, 2, new RstSample([
            '',
            '',
        ])];
        yield [
            'Please use max 2 blank lines, you used 3',
            2,
            new RstSample([
                '',
                '',
                '',
            ]),
        ];
        yield [
            'Please use max 1 blank lines, you used 2',
            1,
            new RstSample([
                '',
                '',
            ]),
        ];

        $invalid = <<<CONTENT
Routing is a system for mapping the URL of incoming requests to the controller
function that should be called to process the request. It both allows you
to specify beautiful URLs and keeps the functionality of your application
decoupled from those URLs. Routing is a bidirectional mechanism, meaning that it
should also be used to generate URLs.

Keep Going!
-----------



Routing, check! Now, uncover the power of :doc:`controllers </controller>`.

Learn more about Routing
------------------------
CONTENT;

        yield [
            'Please use max 2 blank lines, you used 3',
            2,
            new RstSample($invalid, 8),
        ];
    }
}
