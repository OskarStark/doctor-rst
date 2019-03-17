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

use App\Rule\UseDeprecatedDirectiveInsteadOfVersionadded;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class UseDeprecatedDirectiveInsteadOfVersionaddedTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new UseDeprecatedDirectiveInsteadOfVersionadded())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function validProvider()
    {
        return [
            [
                null,
                new RstSample([
                    '.. versionadded:: 3.4',
                    '',
                    '    Foo was added in Symfony 3.4.',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. versionadded:: 3.4',
                    '    Foo was added in Symfony 3.4.',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. deprecated:: 3.4',
                    '',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
            [
                null,
                new RstSample([
                    '.. deprecated:: 3.4',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
        ];
    }

    public function invalidProvider()
    {
        return [
            [
                'Please use ".. deprecated::" instead of ".. versionadded::"',
                new RstSample([
                    '.. versionadded:: 3.4',
                    '',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
            [
                'Please use ".. deprecated::" instead of ".. versionadded::"',
                new RstSample([
                    '.. versionadded:: 3.4',
                    '    Foo was deprecated in Symfony 3.4.',
                ]),
            ],
        ];
    }
}
