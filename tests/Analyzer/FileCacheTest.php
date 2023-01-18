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

use App\Analyzer\FileCache;
use App\Application;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

final class FileCacheTest extends \App\Tests\UnitTestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();
    }

    public function testCacheFileWillBeCreated(): void
    {
        $rstFile = vfsStream::newFile('doc.rst')
            ->withContent('')
            ->at($this->root);

        $cache = new FileCache($this->root->url().'/.doctor-rst.cache');
        $cache->set(new \SplFileInfo($rstFile->url()), ['test'], []);
        $cache->write();

        static::assertTrue($this->root->hasChild('.doctor-rst.cache'));
    }

    public function testCacheHits(): void
    {
        $rules = ['test'];
        $rstFile = vfsStream::newFile('doc.rst')
            ->withContent('')
            ->at($this->root);

        $content = serialize(
            [
                'version' => Application::VERSION,
                'payload' => [
                    $rstFile->url() => [
                        'hash' => sha1_file($rstFile->url()),
                        'rules' => sha1(serialize($rules)),
                        'violations' => [],
                    ],
                ],
            ]
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        static::assertTrue($cache->has(new \SplFileInfo($rstFile->url()), $rules));
    }

    public function testCacheDoesNotHitWhenVersionNumberDoesNotMatch(): void
    {
        $rstFile = vfsStream::newFile('doc.rst')
            ->withContent('')
            ->at($this->root);

        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent(serialize(['version' => 'test', 'payload' => []]))
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        static::assertFalse($cache->has(new \SplFileInfo($rstFile->url()), ['test']));
    }

    public function testCacheDoesNotHitWhenFileHashDoesNotMatch(): void
    {
        $rules = ['test'];
        $rstFile = vfsStream::newFile('doc.rst')
            ->withContent('')
            ->at($this->root);

        $content = serialize(
            [
                'version' => Application::VERSION,
                'payload' => [
                    $rstFile->url() => [
                        'hash' => 'test',
                        'rules' => sha1(serialize($rules)),
                        'violations' => [],
                    ],
                ],
            ]
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        static::assertFalse($cache->has(new \SplFileInfo($rstFile->url()), $rules));
    }

    public function testCacheDoesNotHitWhenRulesHashDoesNotMatch(): void
    {
        $rules = ['test'];
        $rstFile = vfsStream::newFile('doc.rst')
            ->withContent('')
            ->at($this->root);

        $content = serialize(
            [
                'version' => Application::VERSION,
                'payload' => [
                    $rstFile->url() => [
                        'hash' => sha1_file($rstFile->url()),
                        'rules' => 'test',
                        'violations' => [],
                    ],
                ],
            ]
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        static::assertFalse($cache->has(new \SplFileInfo($rstFile->url()), $rules));
    }

    public function testUnparsedFilesWillDeletedFromCacheFile(): void
    {
        $content = serialize(
            [
                'version' => Application::VERSION,
                'payload' => [
                    'doc.rst' => [
                        'hash' => 'test',
                        'rules' => 'test',
                        'violations' => [],
                    ],
                ],
            ]
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());
        $cache->load();
        $cache->write();

        $content = unserialize($cacheFile->getContent());

        static::assertEmpty($content['payload']);
    }
}
