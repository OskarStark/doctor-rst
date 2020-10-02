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

namespace App\Tests\Formatter;

use App\Formatter\GithubFormatter;
use App\Value\AnalyserResult;
use App\Value\ExcludedViolationList;
use App\Value\FileResult;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class GithubFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $analyseDir = \dirname(__DIR__, 2).'/dummy';

        $bufferedOutput = new BufferedOutput();
        $style = new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput);

        $fileResultWithViolations = new FileResult(
            new \SplFileInfo($analyseDir.'/docs/index.rst'),
            new ExcludedViolationList(
                [],
                [Violation::from('violation message', $analyseDir.'/docs/index.rst', 2, 'dummy text')]
            )
        );
        $validFileResult = new FileResult(
            new \SplFileInfo($analyseDir.'/docs/tutorial/introduction.rst'),
            new ExcludedViolationList([], [])
        );

        $analyserResult = new AnalyserResult([$fileResultWithViolations, $validFileResult]);

        (new GithubFormatter())->format($style, $analyserResult, $analyseDir, true);

        $expected = <<<OUTPUT
::error file=$analyseDir/docs/index.rst,line=2::violation message

OUTPUT;

        static::assertSame($expected, $bufferedOutput->fetch());
    }
}
