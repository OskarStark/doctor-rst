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
use App\Value\FileResult;
use App\Value\Violation;
use Symfony\Component\Console\Style\OutputStyle;

class ConsoleFormatter implements Formatter
{
    public function format(
        OutputStyle $style,
        AnalyzerResult $analyzerResult,
        string $analyzeDir,
        bool $showValidFiles,
    ): void {
        $violatedFiles = 0;

        foreach ($analyzerResult->all() as $fileResult) {
            if ($fileResult->violationList()->hasViolations()) {
                ++$violatedFiles;
                $this->formatViolationList($style, $analyzeDir, $fileResult);
                $style->newLine();
            } elseif ($showValidFiles) {
                $this->formatValidFile($style, $analyzeDir, $fileResult);
                $style->newLine();
            }
        }

        if ($unusedWhitelistRegex = $analyzerResult->getUnusedWhitelistRules()['regex']) {
            foreach ($unusedWhitelistRegex as $rule) {
                $style->warning(sprintf(
                    'Whitelisted regex "%s" was not matched.',
                    $rule,
                ));
            }
        }

        if ($unusedWhitelistLines = $analyzerResult->getUnusedWhitelistRules()['lines']) {
            foreach ($unusedWhitelistLines as $rule) {
                $style->warning(sprintf(
                    'Whitelisted line "%s" was not matched.',
                    $rule,
                ));
            }
        }

        if (0 < $violatedFiles) {
            $style->warning(sprintf(
                'Found "%s" invalid %s!',
                $violatedFiles,
                1 === $violatedFiles ? 'file' : 'files',
            ));
        } else {
            $style->success('All files are valid!');
        }
    }

    public function name(): string
    {
        return 'console';
    }

    private function formatViolationList(OutputStyle $style, string $analyzeDir, FileResult $fileResult): void
    {
        $style->writeln(sprintf(
            '%s %s',
            ltrim(str_replace($analyzeDir, '', $fileResult->filename()), '/'),
            sprintf('<fg=red;options=bold>%s</>', "\xE2\x9C\x98" /* HEAVY BALLOT X (U+2718) */),
        ));

        /** @var Violation $violation */
        foreach ($fileResult->violationList()->violations() as $violation) {
            $style->writeln(sprintf(
                '<comment>%s</comment>: %s',
                str_pad((string) $violation->lineno(), 5, ' ', \STR_PAD_LEFT),
                $violation->message(),
            ));

            if (!empty($violation->rawLine())) {
                $style->writeln(sprintf('   <info>-></info>  %s', $violation->rawLine()));
            }
        }
    }

    private function formatValidFile(OutputStyle $style, string $analyzeDir, FileResult $fileResult): void
    {
        $style->writeln(sprintf(
            '%s %s',
            ltrim(str_replace($analyzeDir, '', $fileResult->filename()), '/'),
            sprintf('<fg=green;options=bold>%s</>', "\xE2\x9C\x94" /* HEAVY CHECK MARK (U+2714) */),
        ));
    }
}
