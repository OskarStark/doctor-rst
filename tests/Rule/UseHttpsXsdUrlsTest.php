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

use App\Rule\UseHttpsXsdUrls;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class UseHttpsXsdUrlsTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new UseHttpsXsdUrls())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        yield [NullViolation::create(), new RstSample('')];
        yield [
            NullViolation::create(),
            new RstSample('https://symfony.com/schema/dic/services/services-1.0.xsd'),
        ];
        yield [
            Violation::from(
                'Please use "https" for http://symfony.com/schema/dic/services/services-1.0.xsd',
                'filename',
                1,
                'http://symfony.com/schema/dic/services/services-1.0.xsd',
            ),
            new RstSample('http://symfony.com/schema/dic/services/services-1.0.xsd'),
        ];
    }
}
