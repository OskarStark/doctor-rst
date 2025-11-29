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

namespace App\Formatter;

use App\Value\AnalyzerResult;
use Symfony\Component\Console\Style\OutputStyle;

class GithubFormatter implements Formatter
{
    public function __construct(
        private readonly ConsoleFormatter $consoleFormatter,
    ) {
    }

    public function format(OutputStyle $style, AnalyzerResult $analyzerResult, string $analyzeDir, bool $showValidFiles): void
    {
        $this->consoleFormatter->format($style, $analyzerResult, $analyzeDir, $showValidFiles);

        foreach ($analyzerResult->all() as $fileResult) {
            foreach ($fileResult->violationList()->violations() as $violation) {
                $style->writeln(\sprintf(
                    '::error file=%s,line=%d::%s',
                    $fileResult->filename(),
                    $violation->lineno(),
                    $violation->message(),
                ));
            }
        }

        // Output directory-level violations
        foreach ($analyzerResult->directoryViolations() as $violation) {
            $style->writeln(\sprintf(
                '::error file=%s,line=%d::%s',
                $violation->filename(),
                $violation->lineno(),
                $violation->message(),
            ));
        }
    }

    public function name(): string
    {
        return 'github';
    }
}
