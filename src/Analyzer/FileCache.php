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

namespace App\Analyzer;

use App\Application;
use App\Value\Violation;

final class FileCache implements Cache
{
    /**
     * @var array<string, array{hash: false|string, rules: string, violations: Violation[]}>
     */
    private array $cache = [];
    private bool $loaded = false;

    /**
     * @var array<string, bool>
     */
    private array $parsedFiles = [];

    public function __construct(private readonly string $cacheFile)
    {
    }

    public function has(\SplFileInfo $file, array $rules): bool
    {
        $this->load();

        $pathname = $file->getPathname();

        if (!isset($this->cache[$pathname])) {
            return false;
        }

        if (
            self::hashRules($rules) !== $this->cache[$pathname]['rules']
            || sha1_file($pathname) !== $this->cache[$pathname]['hash']
        ) {
            unset($this->cache[$pathname]);

            return false;
        }

        return true;
    }

    public function get(\SplFileInfo $file, array $rules): array
    {
        $this->load();

        if ($this->has($file, $rules)) {
            $pathname = $file->getPathname();
            $this->parsedFiles[$pathname] = true;

            return $this->cache[$pathname]['violations'];
        }

        return [];
    }

    public function set(\SplFileInfo $file, array $rules, array $violations): void
    {
        $this->load();

        $pathname = $file->getPathname();
        $this->parsedFiles[$pathname] = true;

        $this->cache[$pathname] = [
            'hash' => sha1_file($pathname),
            'rules' => self::hashRules($rules),
            'violations' => $violations,
        ];
    }

    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        if (!file_exists($this->cacheFile) || !is_readable($this->cacheFile)) {
            return;
        }

        $contents = file_get_contents($this->cacheFile);

        if (false === $contents) {
            throw new \RuntimeException(\sprintf('Cache file could not be read "%s".', $this->cacheFile));
        }

        /** @var array{version: string, payload: array<string, array{hash: string, rules: string, violations: Violation[]}>} $cache */
        $cache = unserialize($contents, ['allowed_classes' => [Violation::class]]);

        $this->loaded = true;

        if (Application::VERSION !== $cache['version']) {
            return;
        }

        $this->cache = $cache['payload'];
    }

    public function write(): void
    {
        if (!is_writable(\dirname($this->cacheFile))) {
            return;
        }

        $cache = array_intersect_key($this->cache, $this->parsedFiles);

        file_put_contents(
            $this->cacheFile,
            serialize(
                [
                    'version' => Application::VERSION,
                    'payload' => $cache,
                ],
            ),
        );
    }

    private static function hashRules(array $rules): string
    {
        return sha1(serialize($rules));
    }
}
