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

namespace App\Rule;

use App\Helper\YamlHelper;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class NoBlankLineAfterFilepathInYamlCodeBlock extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YAML)
            && !RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YML)
        ) {
            return NullViolation::create();
        }

        $lines->next();
        ++$number;

        $lines->next();
        ++$number;

        // YML / YAML
        if (preg_match('/^#(.*)\.(yml|yaml)$/', $lines->current()->clean()->toString(), $matches)) {
            return $this->validateBlankLine($lines, $matches, $filename, $number);
        }

        return NullViolation::create();
    }

    private function validateBlankLine(Lines $lines, array $matches, string $filename, int $number): ViolationInterface
    {
        $lines->next();

        if ($lines->current()->isBlank()) {
            $lines->next();
            if (!YamlHelper::isComment($lines->current())) {
                $match = trim($matches[0]);

                return Violation::from(
                    sprintf('Please remove blank line after "%s"', $match),
                    $filename,
                    $number + 1,
                    $match
                );
            }
        }

        return NullViolation::create();
    }
}
