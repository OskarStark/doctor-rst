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

use App\Rule\FileContentRule;
use App\Rule\FileInfoRule;
use App\Rule\LineContentRule;
use App\Rule\Rule;
use App\Rule\RuleFilter;
use App\Value\Line;
use App\Value\Lines;
use App\Value\ViolationInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @no-named-arguments
 */
final class RstAnalyzer implements Analyzer
{
    /**
     * @param Rule[] $rules
     *
     * @return ViolationInterface[]
     */
    public function analyze(\SplFileInfo $file, array $rules): array
    {
        $realpath = $file->getRealPath();

        if (false === $realpath) {
            throw new \RuntimeException(\sprintf('Cannot get real path for file: %s', (string) $file->getPathname()));
        }

        $content = file($realpath);

        if (false === $content) {
            throw new \RuntimeException(\sprintf('Cannot parse file: %s', (string) $realpath));
        }

        $violations = [];

        $fileInfoRules = RuleFilter::byType($rules, FileInfoRule::class);

        foreach ($fileInfoRules as $rule) {
            $violation = $rule->check($file);

            if (!$violation->isNull()) {
                $violations[] = $violation;
            }
        }

        $fileContentRules = RuleFilter::byType($rules, FileContentRule::class);

        $lines = Lines::fromArray($content);

        foreach ($fileContentRules as $rule) {
            $violation = $rule->check(clone $lines, $realpath);

            if (!$violation->isNull()) {
                $violations[] = $violation;
            }

            if ($rule instanceof ResetInterface) {
                $rule->reset();
            }
        }

        $lineContentRules = RuleFilter::byType($rules, LineContentRule::class);

        /**
         * @var int  $no
         * @var Line $line
         */
        foreach ($lines->toIterator() as $no => $line) {
            $lineIsBlank = $line->isBlank();

            foreach ($lineContentRules as $rule) {
                if ($lines->isProcessedBy($no, $rule::class)) {
                    continue;
                }

                if (!$rule::runOnlyOnBlankline() && $lineIsBlank) {
                    continue;
                }

                $violation = $rule->check($lines, $no, $realpath);

                if (!$violation->isNull()) {
                    $violations[] = $violation;
                }

                if ($rule instanceof ResetInterface) {
                    $rule->reset();
                }
            }
        }

        return $violations;
    }
}
