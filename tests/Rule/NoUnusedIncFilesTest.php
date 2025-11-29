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

use App\Rule\NoUnusedIncFiles;
use App\Tests\UnitTestCase;
use App\Value\Violation;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class NoUnusedIncFilesTest extends UnitTestCase
{
    private string $tempDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->tempDir = sys_get_temp_dir().'/doctor-rst-test-'.uniqid();
        $this->filesystem->mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->tempDir);
    }

    #[Test]
    public function noViolationWhenNoIncFiles(): void
    {
        $this->filesystem->dumpFile($this->tempDir.'/index.rst', 'Hello World');

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertSame([], $violations);
    }

    #[Test]
    public function noViolationWhenIncFileIsUsedWithInclude(): void
    {
        $this->filesystem->dumpFile($this->tempDir.'/included.rst.inc', 'Included content');
        $this->filesystem->dumpFile(
            $this->tempDir.'/index.rst',
            <<<'RST'
Main content

.. include:: included.rst.inc
RST
        );

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertSame([], $violations);
    }

    #[Test]
    public function noViolationWhenIncFileIsUsedWithLiteralinclude(): void
    {
        $this->filesystem->dumpFile($this->tempDir.'/code.rst.inc', 'Code content');
        $this->filesystem->dumpFile(
            $this->tempDir.'/index.rst',
            <<<'RST'
Main content

.. literalinclude:: code.rst.inc
RST
        );

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertSame([], $violations);
    }

    #[Test]
    public function violationWhenIncFileIsNotUsed(): void
    {
        $this->filesystem->dumpFile($this->tempDir.'/unused.rst.inc', 'Unused content');
        $this->filesystem->dumpFile($this->tempDir.'/index.rst', 'Main content without includes');

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertCount(1, $violations);
        self::assertInstanceOf(Violation::class, $violations[0]);
        self::assertSame('The file "unused.rst.inc" is not included anywhere and should be removed.', $violations[0]->message());
    }

    #[Test]
    public function multipleUnusedIncFiles(): void
    {
        $this->filesystem->dumpFile($this->tempDir.'/unused1.rst.inc', 'Unused content 1');
        $this->filesystem->dumpFile($this->tempDir.'/unused2.rst.inc', 'Unused content 2');
        $this->filesystem->dumpFile($this->tempDir.'/used.rst.inc', 'Used content');
        $this->filesystem->dumpFile(
            $this->tempDir.'/index.rst',
            <<<'RST'
Main content

.. include:: used.rst.inc
RST
        );

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertCount(2, $violations);
    }

    #[Test]
    public function noViolationWhenIncFileIsUsedWithAbsolutePath(): void
    {
        $this->filesystem->mkdir($this->tempDir.'/_includes');
        $this->filesystem->dumpFile($this->tempDir.'/_includes/shared.rst.inc', 'Shared content');
        $this->filesystem->dumpFile(
            $this->tempDir.'/index.rst',
            <<<'RST'
Main content

.. include:: /_includes/shared.rst.inc
RST
        );

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertSame([], $violations);
    }

    #[Test]
    public function noViolationWhenIncFileIsUsedFromSubdirectory(): void
    {
        $this->filesystem->mkdir($this->tempDir.'/docs');
        $this->filesystem->dumpFile($this->tempDir.'/docs/snippet.rst.inc', 'Snippet content');
        $this->filesystem->dumpFile(
            $this->tempDir.'/docs/index.rst',
            <<<'RST'
Main content

.. include:: snippet.rst.inc
RST
        );

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertSame([], $violations);
    }

    #[Test]
    public function noViolationWhenIncFileIsUsedWithRelativePathFromSubdirectory(): void
    {
        $this->filesystem->mkdir($this->tempDir.'/_includes');
        $this->filesystem->mkdir($this->tempDir.'/docs');
        $this->filesystem->dumpFile($this->tempDir.'/_includes/shared.rst.inc', 'Shared content');
        $this->filesystem->dumpFile(
            $this->tempDir.'/docs/index.rst',
            <<<'RST'
Main content

.. include:: ../_includes/shared.rst.inc
RST
        );

        $finder = new Finder();
        $finder->files()->name(['*.rst', '*.rst.inc'])->in($this->tempDir);

        $rule = new NoUnusedIncFiles();
        $violations = $rule->check($finder, $this->tempDir);

        self::assertSame([], $violations);
    }
}
