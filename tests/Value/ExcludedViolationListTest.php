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

namespace App\Tests\Value;

use App\Tests\UnitTestCase;
use App\Value\ExcludedViolationList;
use App\Value\Violation;
use PHPUnit\Framework\Attributes\Test;

final class ExcludedViolationListTest extends UnitTestCase
{
    #[Test]
    public function filterExcludedViolations(): void
    {
        $filename = \dirname(__DIR__, 2).'/dummy/docs/index.rst';

        $list = new ExcludedViolationList(
            [
                'regex' => [
                    '/regex/',
                ],
                'lines' => [
                    'excluded line',
                ],
            ],
            [
                $dummy = Violation::from('violation message', $filename, 2, 'dummy text'),
                Violation::from('violation message', $filename, 3, 'excluded line'),
                Violation::from('violation message', $filename, 4, 'excluded regex'),
                Violation::from('violation message', $filename, 4, 'excluded regex'),
            ],
        );

        self::assertSame([$dummy], $list->violations());
        self::assertSame(
            [
                'excluded line' => 1,
            ],
            $list->getMatchedWhitelistLines(),
        );
        self::assertSame(
            [
                '/regex/' => 2,
            ],
            $list->getMatchedWhitelistRegex(),
        );
    }
}
