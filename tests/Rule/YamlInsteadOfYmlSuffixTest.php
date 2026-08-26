<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\YamlInsteadOfYmlSuffix;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class YamlInsteadOfYmlSuffixTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new YamlInsteadOfYmlSuffix())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Iterator<(int|string), array{ViolationInterface, RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            Violation::from(
                'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"',
                'filename',
                1,
                '.. code-block:: yml',
            ),
            new RstSample('.. code-block:: yml'),
        ];
        yield [
            NullViolation::create(),
            new RstSample('.. code-block:: yaml'),
        ];
        yield [
            NullViolation::create(),
            new RstSample('.travis.yml'),
        ];
        yield [
            Violation::from(
                'Please use ".yaml" instead of ".yml"',
                'filename',
                1,
                'Register your service in services.yml file',
            ),
            new RstSample('Register your service in services.yml file'),
        ];
        yield [
            NullViolation::create(),
            new RstSample('Register your service in services.yaml file'),
        ];
    }
}
