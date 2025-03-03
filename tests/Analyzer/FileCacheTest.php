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

use App\Analyzer\FileCache;
use App\Application;
use App\Rule\ShortArraySyntax;
use App\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\Test;

final class FileCacheTest extends UnitTestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();
    }

    #[Test]
    public function cacheFileWillBeCreated(): void
    {
        $rstFile = vfsStream::newFile('doc.rst')
            ->withContent('')
            ->at($this->root);

        $cache = new FileCache($this->root->url().'/.doctor-rst.cache');
        $cache->set(new \SplFileInfo($rstFile->url()), [new ShortArraySyntax()], []);
        $cache->write();

        self::assertTrue($this->root->hasChild('.doctor-rst.cache'));
    }

    #[Test]
    public function cacheHits(): void
    {
        $rules = [new ShortArraySyntax()];
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
            ],
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        self::assertTrue($cache->has(new \SplFileInfo($rstFile->url()), $rules));
    }

    #[Test]
    public function cacheDoesNotHitWhenVersionNumberDoesNotMatch(): void
    {
        $rstFile = vfsStream::newFile('doc.rst')
            ->withContent('')
            ->at($this->root);

        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent(serialize(['version' => 'test', 'payload' => []]))
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        self::assertFalse($cache->has(new \SplFileInfo($rstFile->url()), [new ShortArraySyntax()]));
    }

    #[Test]
    public function cacheDoesNotHitWhenFileHashDoesNotMatch(): void
    {
        $rules = [new ShortArraySyntax()];
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
            ],
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        self::assertFalse($cache->has(new \SplFileInfo($rstFile->url()), $rules));
    }

    #[Test]
    public function cacheDoesNotHitWhenRulesHashDoesNotMatch(): void
    {
        $rules = [new ShortArraySyntax()];
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
            ],
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());

        self::assertFalse($cache->has(new \SplFileInfo($rstFile->url()), $rules));
    }

    #[Test]
    public function unparsedFilesWillDeletedFromCacheFile(): void
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
            ],
        );
        $cacheFile = vfsStream::newFile('.doctor-rst.cache')
            ->withContent($content)
            ->at($this->root);

        $cache = new FileCache($cacheFile->url());
        $cache->load();
        $cache->write();

        $content = unserialize($cacheFile->getContent());

        self::assertIsArray($content);
        self::assertArrayHasKey('payload', $content);
        self::assertEmpty($content['payload']);
    }
}
