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
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure the content of :ref: and :doc: directives is surrounded by a single backtick.')]
#[InvalidExample(':ref:`DeprecatedAlias <routing-alias-deprecation>``')]
#[ValidExample(':doc:`Route </routing>`')]
final class UseSingleBackticksForRefAndDoc extends AbstractRule implements LineContentRule
{
    use DirectiveTrait;

    /**
     * Matches a :ref: or :doc: directive with its surrounding backticks.
     *
     * "before" tells apart a directive shown inside an inline literal, as in
     * ``:ref:`Foo```, from a directive whose own backticks are doubled.
     */
    private const string PATTERN = '/(?P<before>`*):(?P<role>ref|doc):(?P<open>`+)(?P<content>[^`]*)(?P<close>`+)/';

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

        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number)) {
            return NullViolation::create();
        }

        if (!preg_match_all(self::PATTERN, $line->raw()->toString(), $matches, \PREG_SET_ORDER)) {
            return NullViolation::create();
        }

        foreach ($matches as $match) {
            // the directive is quoted as an inline literal, its backticks are not its own
            if ('' !== $match['before']) {
                continue;
            }

            if (1 === \strlen($match['open']) && 1 === \strlen($match['close'])) {
                continue;
            }

            return Violation::from(
                \sprintf(
                    'Please use a single backtick around "%s" inside :%s: directive',
                    $match['content'],
                    $match['role'],
                ),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
