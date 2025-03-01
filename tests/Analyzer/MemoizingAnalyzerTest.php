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

use App\Analyzer\Analyzer;
use App\Analyzer\Cache;
use App\Analyzer\MemoizingAnalyzer;
use App\Tests\Fixtures\Rule\DummyRule;
use App\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class MemoizingAnalyzerTest extends UnitTestCase
{
    /**
     * @var Analyzer|MockObject
     */
    private MockObject $analyzer;

    /**
     * @var Cache|MockObject
     */
    private MockObject $cache;
    private MemoizingAnalyzer $memoizingAnalyzer;

    protected function setUp(): void
    {
        $this->analyzer = $this->createMock(Analyzer::class);
        $this->cache = $this->createMock(Cache::class);
        $this->memoizingAnalyzer = new MemoizingAnalyzer($this->analyzer, $this->cache);
    }

    #[Test]
    public function cacheHitReturnsCacheContent(): void
    {
        $fileInfo = new \SplFileInfo('test.rst');
        $rules = [
            new DummyRule(),
        ];

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with($fileInfo, $rules)
            ->willReturn(true);

        $this->cache
            ->expects(self::once())
            ->method('get')
            ->with($fileInfo, $rules)
            ->willReturn([]);

        $this->analyzer->expects(self::never())->method('analyze');

        $this->memoizingAnalyzer->analyze($fileInfo, $rules);
    }

    #[Test]
    public function noCacheHitCallsAnalyzerAndSavesResultsToCache(): void
    {
        $fileInfo = new \SplFileInfo('test.rst');
        $rules = [
            new DummyRule(),
        ];

        $this->cache->expects(self::never())->method('get');

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with($fileInfo, $rules)
            ->willReturn(false);

        $this->cache
            ->expects(self::once())
            ->method('set')
            ->with($fileInfo, $rules, []);

        $this->analyzer
            ->expects(self::once())
            ->method('analyze')
            ->with($fileInfo, $rules)
            ->willReturn([]);

        $this->memoizingAnalyzer->analyze($fileInfo, $rules);
    }
}
