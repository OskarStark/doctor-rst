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

namespace App\Analyzer;

use App\Rule\FileContentRule;
use App\Rule\LineContentRule;
use App\Rule\Rule;
use App\Value\Line;
use App\Value\Lines;
use App\Value\Violation;
use SplFileInfo;
use Symfony\Contracts\Service\ResetInterface;

final class RstAnalyzer implements Analyzer
{
    /**
     * @param Rule[] $rules
     *
     * @return Violation[]
     */
    public function analyze(SplFileInfo $file, array $rules): array
    {
        $realpath = $file->getRealPath();
        if (false === $realpath) {
            throw new \RuntimeException(sprintf('Cannot get real path for file: %s', (string) $file->getPathname()));
        }

        $content = file($realpath);

        if (false === $content) {
            throw new \RuntimeException(sprintf('Cannot parse file: %s', (string) $file->getRealPath()));
        }

        $lines = Lines::fromArray($content);

        $violations = [];

        /**
         * @var int $no
         * @var Line $line
         */
        foreach ($lines->toIterator() as $no => $line) {
            \assert(\is_int($no));

            foreach ($rules as $rule) {
                if ($lines->isProcessedBy($no, \get_class($rule))) {
                    continue;
                }

                if (!$rule::runOnlyOnBlankline() && $line->isBlank()) {
                    continue;
                }

                if ($rule instanceof LineContentRule) {
                    $violationMessage = $rule->check($lines, $no);
                } elseif ($rule instanceof FileContentRule) {
                    if ($no > 0) {
                        continue;
                    }

                    $violationMessage = $rule->check($lines);
                } else {
                    throw new \RuntimeException('Unknown type of rule provided!');
                }

                if (null !== $violationMessage) {
                    $violations[] = Violation::from(
                        $violationMessage,
                        (string) $file->getRealPath(),
                        $no + 1,
                        $rule instanceof FileContentRule ? '' : $line->raw()->trim()->toString()
                    );
                }

                if ($rule instanceof ResetInterface) {
                    $rule->reset();
                }
            }
        }

        return $violations;
    }
}
