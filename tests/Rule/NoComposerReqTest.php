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

use App\Rule\NoComposerReq;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class NoComposerReqTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoComposerReq())->check($sample->lines(), $sample->lineNumber(), 'filename'),
        );
    }

    public static function checkProvider(): array
    {
        return [
            [
                Violation::from(
                    'Please "composer require" instead of "composer req"',
                    'filename',
                    1,
                    'composer req symfony/form',
                ),
                new RstSample('composer req symfony/form'),
            ],
            [
                NullViolation::create(),
                new RstSample('composer require symfony/form'),
            ],
        ];
    }
}
