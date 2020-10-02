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

namespace App\Formatter;

use App\Value\AnalyserResult;
use Symfony\Component\Console\Style\OutputStyle;

class GithubFormatter implements Formatter
{
    public function format(OutputStyle $style, AnalyserResult $analyserResult, string $analyseDir, bool $showValidFiles): void
    {
        foreach ($analyserResult->all() as $fileResult) {
            foreach ($fileResult->violationList()->violations() as $violation) {
                $style->writeln(sprintf(
                    '::error file=%s,line=%s::%s',
                    $fileResult->filename(),
                    $violation->lineno(),
                    $violation->message(),
                ));
            }
        }
    }

    public function name(): string
    {
        return 'github';
    }
}
