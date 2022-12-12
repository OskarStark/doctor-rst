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

namespace App\Tests\Analyzer;

use App\Analyzer\Analyzer;
use App\Analyzer\Cache;
use App\Analyzer\MemoizingAnalyzer;
use App\Tests\Fixtures\Rule\DummyRule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

final class MemoizingAnalyzerTest extends TestCase
{
    /**
     * @var Analyzer|MockObject
     */
    private $analyzer;
    /**
     * @var Cache|MockObject
     */
    private $cache;

    private MemoizingAnalyzer $memoizingAnalyzer;

    protected function setUp(): void
    {
        $this->analyzer = $this->createMock(Analyzer::class);
        $this->cache = $this->createMock(Cache::class);
        $this->memoizingAnalyzer = new MemoizingAnalyzer($this->analyzer, $this->cache);
    }

    public function testCacheHitReturnsCacheContent(): void
    {
        $fileInfo = new SplFileInfo('test.rst');
        $rules = [
            new DummyRule(),
        ];

        $this->cache
            ->expects(static::once())
            ->method('has')
            ->with($fileInfo, $rules)
            ->willReturn(true);

        $this->cache
            ->expects(static::once())
            ->method('get')
            ->with($fileInfo, $rules)
            ->willReturn([]);

        $this->analyzer->expects(static::never())->method('analyze');

        $this->memoizingAnalyzer->analyze($fileInfo, $rules);
    }

    public function testNoCacheHitCallsAnalyzerAndSavesResultsToCache(): void
    {
        $fileInfo = new SplFileInfo('test.rst');
        $rules = [
            new DummyRule(),
        ];

        $this->cache->expects(static::never())->method('get');

        $this->cache
            ->expects(static::once())
            ->method('has')
            ->with($fileInfo, $rules)
            ->willReturn(false);

        $this->cache
            ->expects(static::once())
            ->method('set')
            ->with($fileInfo, $rules, []);

        $this->analyzer
            ->expects(static::once())
            ->method('analyze')
            ->with($fileInfo, $rules)
            ->willReturn([]);

        $this->memoizingAnalyzer->analyze($fileInfo, $rules);
    }
}
