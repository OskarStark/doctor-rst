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

namespace App\Tests\Analyser;

use App\Analyser\Analyser;
use App\Analyser\Cache;
use App\Analyser\MemoizingAnalyser;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class MemoizingAnalyserTest extends TestCase
{
    /**
     * @var Analyser|\PHPUnit\Framework\MockObject\MockObject
     */
    private $analyser;
    /**
     * @var Cache|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cache;

    private MemoizingAnalyser $memoizingAnalyser;

    protected function setUp(): void
    {
        $this->analyser = $this->createMock(Analyser::class);
        $this->cache = $this->createMock(Cache::class);
        $this->memoizingAnalyser = new MemoizingAnalyser($this->analyser, $this->cache);
    }

    public function testCacheHitReturnsCacheContent(): void
    {
        $fileInfo = new SplFileInfo('test.rst');
        $rules = ['test'];

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

        $this->analyser->expects(static::never())->method('analyse');

        $this->memoizingAnalyser->analyse($fileInfo, $rules);
    }

    public function testNoCacheHitCallsAnalyserAndSavesResultsToCache(): void
    {
        $fileInfo = new SplFileInfo('test.rst');
        $rules = ['test'];

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

        $this->analyser
            ->expects(static::once())
            ->method('analyse')
            ->with($fileInfo, $rules)
            ->willReturn([]);

        $this->memoizingAnalyser->analyse($fileInfo, $rules);
    }
}
