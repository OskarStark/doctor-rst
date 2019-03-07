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

namespace app\tests\Rule;

use App\Rule\VersionaddedDirectiveShouldHaveVersion;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\TestCase;

class VersionaddedDirectiveShouldHaveVersionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, $line)
    {
        $this->assertSame(
            $expected,
            (new VersionaddedDirectiveShouldHaveVersion(new VersionParser()))->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                '.. versionadded:: 1',
            ],
            [
                null,
                '.. versionadded:: 1.2',
            ],
            [
                null,
                '.. versionadded:: 1.2.0',
            ],
            [
                null,
                '.. versionadded:: 1.2   ',
            ],
            [
                'Please provide a version behind ".. versionadded::"',
                '.. versionadded::',
            ],
            [
                'Please provide a numeric version behind ".. versionadded::" instead of "foo"',
                '.. versionadded:: foo',
            ],
        ];
    }
}
