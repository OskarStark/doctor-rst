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

use App\Rule\DeprecatedDirectiveShouldHaveVersion;
use App\Tests\RstSample;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\TestCase;

class DeprecatedDirectiveShouldHaveVersionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new DeprecatedDirectiveShouldHaveVersion(new VersionParser()))
                ->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                new RstSample('.. deprecated:: 1'),
            ],
            [
                null,
                new RstSample('.. deprecated:: 1.2'),
            ],
            [
                null,
                new RstSample('.. deprecated:: 1.2.0'),
            ],
            [
                null,
                new RstSample('.. deprecated:: 1.2   '),
            ],
            [
                'Please provide a version behind ".. deprecated::"',
                new RstSample('.. deprecated::'),
            ],
            [
                'Please provide a numeric version behind ".. deprecated::" instead of "foo"',
                new RstSample('.. deprecated:: foo'),
            ],
        ];
    }
}
