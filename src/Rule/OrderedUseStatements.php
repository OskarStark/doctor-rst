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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

use function Symfony\Component\String\u;

use Webmozart\Assert\Assert;

class OrderedUseStatements extends AbstractRule implements LineContentRule
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
            && ($indention < $lines->current()->indention() || $lines->current()->isBlank())
            && (!preg_match('/^((class|trait) (.*)|\$)/', $lines->current()->clean()->toString()))
        ) {
            if ($lines->current()->clean()->match('/^use (.*);$/')) {
                if (null === $indentionOfFirstFoundUseStatement) {
                    $indentionOfFirstFoundUseStatement = $lines->current()->indention();
                    $statements[] = $this->extractClass($lines->current()->clean()->toString());
                } else {
                    if ($indentionOfFirstFoundUseStatement !== $lines->current()->indention()) {
                        break;
                    }

                    $statements[] = $this->extractClass($lines->current()->clean()->toString());
                }
            }

            $lines->next();
        }

        if (empty($statements) || 1 === \count($statements)) {
            return NullViolation::create();
        }

        $sortedUseStatements = $statements;

        natsort($sortedUseStatements);

        if ($statements !== $sortedUseStatements) {
            return Violation::from(
                'Please reorder the use statements alphabetically',
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }

    private function extractClass(string $useStatement): string
    {
        $matches = u($useStatement)->match('/use (.*);/');

        Assert::keyExists($matches, 1);

        return u($matches[1])
            ->trim()
            ->replace('\\', 'A') // the "A" here helps to sort !!
            ->lower()
            ->toString();
    }
}
