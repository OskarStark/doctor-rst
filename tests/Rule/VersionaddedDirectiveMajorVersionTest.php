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

use App\Rule\VersionaddedDirectiveMajorVersion;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\TestCase;

class VersionaddedDirectiveMajorVersionTest extends TestCase
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
            (new VersionaddedDirectiveMajorVersion(new VersionParser()))->check(new \ArrayIterator([$line]), 0)
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                '.. versionadded:: 3',
            ],
            [
                null,
                '.. versionadded:: 3.4',
            ],
            [
                null,
                '.. versionadded:: 3.4.0',
            ],
            [
                null,
                '.. versionadded:: 3.4.0.0',
            ],
            [
                null,
                '.. versionadded:: 3.4   ',
            ],
            [
                'You are not allowed to use version "2.7". Only major version "3" is allowed.',
                '.. versionadded:: 2.7',
            ],
            [
                'You are not allowed to use version "4.0". Only major version "3" is allowed.',
                '.. versionadded:: 4.0',
            ],
            [
                'Please provide a numeric version behind ".. versionadded::" instead of "foo"',
                '.. versionadded:: foo',
            ],
        ];
    }
}
