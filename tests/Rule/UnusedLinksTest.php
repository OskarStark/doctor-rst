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

use App\Rule\UnusedLinks;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class UnusedLinksTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new UnusedLinks())->check($sample->lines(), $sample->lineNumber())
        );
    }

    public function validProvider(): \Generator
    {
        yield [
            null,
            new RstSample('this is a test'),
        ];

        yield [
            null,
            new RstSample(<<<RST
I am a `Link`_

.. _`Link`: https://example.com
RST
            ),
        ];

        yield [
            null,
            new RstSample(<<<RST
I am a `Link`_ and `Link2`_

.. _`Link`: https://example.com
.. _`Link2`: https://example2.com
RST
            ),
        ];

        yield [
            null,
            new RstSample(<<<RST
I am a `Link`_

.. _Link: https://example.com
RST
            ),
        ];

        yield [
            null,
            new RstSample(<<<RST
I am a `Link`_ and `Link2`_

.. _Link: https://example.com
.. _Link2: https://example2.com
RST
            ),
        ];

        yield [
            null,
            new RstSample(<<<RST
I am `a Link`_, `some other Link`_ and Link2_

.. _a Link: https://example.com
.. _Link2: https://example2.com
.. _`some other Link`: https://example3.com
RST
            ),
        ];
    }

    public function invalidProvider(): \Generator
    {
        yield [
            'The following link definitions aren\'t used anymore and should be removed: unused',
            new RstSample(<<<RST
I am a `Link`_

.. _`Link`: https://example.com
.. _`unused`: https://404.com
RST
            ),
        ];

        yield [
            'The following link definitions aren\'t used anymore and should be removed: unused',
            new RstSample(<<<RST
I am a `Link`_

.. _Link: https://example.com
.. _unused: https://404.com
RST
            ),
        ];
    }
}
