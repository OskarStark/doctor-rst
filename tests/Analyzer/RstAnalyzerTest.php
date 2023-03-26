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

namespace App\Tests\Analyzer;

use App\Analyzer\RstAnalyzer;
use App\Rule\MaxBlankLines;

final class RstAnalyzerTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function onlyOneMaxBlankLineViolationMessageOccurs(): void
    {
        $maxBlankLines = new MaxBlankLines();
        $maxBlankLines->setOptions(['max' => 3]);

        $violations = (new RstAnalyzer())->analyze(
            new \SplFileInfo(__DIR__.'/../Fixtures/max_blanklines.rst'),
            [$maxBlankLines],
        );

        self::assertCount(1, $violations);
    }
}
