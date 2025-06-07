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
use App\Value\Line;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure code blocks are indented by 4 spaces.')]
#[InvalidExample('   .. code-block:: yml')]
#[ValidExample('  .. code-block:: yml')]
class EnsureCodeBlockIndentation extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return NullViolation::create();
        }

        $spaces = $this->countSpaces($line);

        if (0 !== ($spaces % 4)) {
            $message = \sprintf(
                'Please indent code block with multiple of 4 spaces, or zero. Actually %d space(s)',
                $spaces,
            );

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }

    private function countSpaces(Line $line): int
    {
        $matches = $line->raw()->match('/^(?<spaces>\s*)\.\./');

        if (!$matches) {
            return 0;
        }

        return mb_strlen($matches['spaces']);
    }
}
