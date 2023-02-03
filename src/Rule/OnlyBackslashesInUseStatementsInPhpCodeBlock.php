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

use App\Annotations\Rule\Description;
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("A use statement in a PHP code-block should only contain backslashes.")
 *
 * @InvalidExample("use Foo/Bar;")
 *
 * @ValidExample("use Foo\Bar;")
 */
class OnlyBackslashesInUseStatementsInPhpCodeBlock extends AbstractRule implements LineContentRule
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

        if ($line->clean()->lower()->startsWith('use ')
            && $line->clean()->lower()->endsWith(';')
            && $line->clean()->containsAny('/')
            && $this->inPhpCodeBlock($lines, $number)
        ) {
            $message = sprintf(
                'Please check "%s", it should not contain "/"',
                $line->clean()->toString()
            );

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                $line
            );
        }

        return NullViolation::create();
    }
}
