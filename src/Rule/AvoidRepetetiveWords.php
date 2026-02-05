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

namespace App\Rule;

use App\Attribute\Rule\Description;
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Helper\PhpHelper;
use App\Helper\TwigHelper;
use App\Helper\XmlHelper;
use App\Helper\YamlHelper;
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Make sure that a word is not used twice in a row.')]
#[ValidExample('Please do not use it this way...')]
#[InvalidExample('Please do not not use it this way...')]
final class AvoidRepetetiveWords extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->isDirective()
            || RstParser::isLinkDefinition($line)
            || $line->isBlank()
            || RstParser::isTable($line)
            || ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number) && (
                !PhpHelper::isComment($line)
                && !XmlHelper::isComment($line)
                && !TwigHelper::isComment($line)
                && !YamlHelper::isComment($line)
            ))
        ) {
            return NullViolation::create();
        }

        $words = explode(' ', $line->clean()->toString());

        foreach ($words as $key => $word) {
            if (0 === $key) {
                continue;
            }

            if (\in_array($word, self::whitelist(), true)) {
                continue;
            }

            if (isset($words[$key + 1]) && $words[$key + 1] !== '' && $words[$key + 1] !== '0' && 1 < \strlen($word) && $words[$key + 1] === $word && (!is_numeric(rtrim($word, ',')))) {
                $message = \sprintf('The word "%s" is used more times in a row.', $word);

                return Violation::from(
                    $message,
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        return NullViolation::create();
    }

    private static function whitelist(): array
    {
        return [
            '...',
        ];
    }
}
