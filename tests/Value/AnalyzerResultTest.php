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
use App\Value\AnalyzerResult;
use App\Value\ExcludedViolationList;
use App\Value\FileResult;
use App\Value\Violation;
use PHPUnit\Framework\Attributes\Test;

final class AnalyzerResultTest extends UnitTestCase
{
    #[Test]
    public function all(): void
    {
        $filename = \dirname(__DIR__, 2).'/dummy/docs/index.rst';

        $excludedViolationConfig = [
            'regex' => [
                '/regex/',
                '/regex_not_match/',
                '/regex_not_match_bis/',
            ],
            'lines' => [
                'excluded line',
                'excluded line not match',
                'excluded line not match bis',
            ],
        ];

        $excludedViolationListOne = new ExcludedViolationList(
            $excludedViolationConfig,
            [
                Violation::from('violation message', $filename, 2, 'dummy text'),
                Violation::from('violation message', $filename, 3, 'excluded line'),
                Violation::from('violation message', $filename, 4, 'excluded regex'),
                Violation::from('violation message', $filename, 4, 'excluded regex'),
            ],
        );

        $excludedViolationListTwo = new ExcludedViolationList(
            $excludedViolationConfig,
            [
                Violation::from('violation message', $filename, 2, 'dummy text'),
                Violation::from('violation message', $filename, 3, 'excluded line'),
                Violation::from('violation message', $filename, 4, 'excluded regex'),
            ],
        );

        $analyserResult = new AnalyzerResult(
            [
                new FileResult(
                    $this->createMock(\SplFileInfo::class),
                    $excludedViolationListOne,
                ),
                new FileResult(
                    $this->createMock(\SplFileInfo::class),
                    $excludedViolationListTwo,
                ),
            ],
            $excludedViolationConfig,
        );

        self::assertSame(
            [
                'regex' => [
                    '/regex_not_match/',
                    '/regex_not_match_bis/',
                ],
                'lines' => [
                    'excluded line not match',
                    'excluded line not match bis',
                ],
            ],
            $analyserResult->getUnusedWhitelistRules(),
        );
    }
}
