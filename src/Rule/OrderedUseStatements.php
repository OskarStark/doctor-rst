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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

final class OrderedUseStatements extends AbstractRule implements LineContentRule
{
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

        if (!RstParser::isPhpDirective($line)) {
            return NullViolation::create();
        }

        $indention = $line->indention();

        $lines->next();

        $statements = [];
        $indentionOfFirstFoundUseStatement = null;

        while ($lines->valid()
            && !$lines->current()->isDirective()
            && ($lines->current()->indention() > $indention || $lines->current()->isBlank())
            && (!preg_match('/^((class|trait) (.*)|\$)/', $lines->current()->clean()->toString()))
        ) {
            if ($lines->current()->clean()->match('/^use (.*);$/')) {
                if (null === $indentionOfFirstFoundUseStatement) {
                    $indentionOfFirstFoundUseStatement = $lines->current()->indention();
                    $statements[] = self::extractClass($lines->current()->clean()->toString());
                } else {
                    if ($lines->current()->indention() !== $indentionOfFirstFoundUseStatement) {
                        break;
                    }

                    $statements[] = self::extractClass($lines->current()->clean()->toString());
                }
            }

            $lines->next();
        }

        if ([] === $statements || 1 === \count($statements)) {
            return NullViolation::create();
        }

        $sortedUseStatements = $statements;

        natsort($sortedUseStatements);

        if ($statements !== $sortedUseStatements) {
            return Violation::from(
                'Please reorder the use statements alphabetically',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }

    private static function extractClass(string $useStatement): string
    {
        $matches = u($useStatement)->match('/use (.*);/');
        /** @var string[] $matches */
        Assert::keyExists($matches, 1);

        return u($matches[1])
            ->trim()
            ->replace('\\', 'A') // the "A" here helps to sort !!
            ->lower()
            ->replace('function ', 'zzzfunction') // prefix with "zzz" to sort functions at the end
            ->toString();
    }
}
