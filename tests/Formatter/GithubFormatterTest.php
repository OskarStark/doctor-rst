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

namespace App\Tests\Formatter;

use App\Formatter\ConsoleFormatter;
use App\Formatter\GithubFormatter;
use App\Tests\UnitTestCase;
use App\Value\AnalyzerResult;
use App\Value\ExcludedViolationList;
use App\Value\FileResult;
use App\Value\Violation;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GithubFormatterTest extends UnitTestCase
{
    #[Test]
    public function format(): void
    {
        $analyzeDir = \dirname(__DIR__, 2).'/dummy';

        $bufferedOutput = new BufferedOutput();
        $style = new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput);

        $fileResultWithViolations = new FileResult(
            new \SplFileInfo($analyzeDir.'/docs/index.rst'),
            new ExcludedViolationList(
                [],
                [Violation::from('violation message', $analyzeDir.'/docs/index.rst', 2, 'dummy text')],
            ),
        );
        $validFileResult = new FileResult(
            new \SplFileInfo($analyzeDir.'/docs/tutorial/introduction.rst'),
            new ExcludedViolationList([], []),
        );

        $analyzerResult = new AnalyzerResult([$fileResultWithViolations, $validFileResult], [
            'regex' => [
                '/foo/',
            ],
        ]);

        (new GithubFormatter(new ConsoleFormatter()))->format($style, $analyzerResult, $analyzeDir, false);

        $expected = <<<OUTPUT
docs/index.rst ✘
    2: violation message
   ->  dummy text

 [WARNING] Whitelisted regex "/foo/" was not matched.                           

 [WARNING] Found "1" invalid file!                                              

::error file={$analyzeDir}/docs/index.rst,line=2::violation message

OUTPUT;

        self::assertSame($expected, $bufferedOutput->fetch());
    }
}
