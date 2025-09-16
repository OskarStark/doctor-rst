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

/**
 * @no-named-arguments
 */
interface Formatter
{
    public function format(OutputStyle $style, AnalyzerResult $analyzerResult, string $analyzeDir, bool $showValidFiles): void;

    public function name(): string;
}
