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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure php reference syntax is valid.')]
#[InvalidExample('The :class:`Symfony\\Component\\Notifier\\Transport`` class')]
#[ValidExample('The :class:`Symfony\\Component\\Notifier\\Transport` class')]
class EnsurePhpReferenceSyntax extends AbstractRule implements LineContentRule
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

        if ($line->clean()->match('/:(class|method|namespace|phpclass|phpfunction|phpmethod):`[a-zA-Z\\\\:]+``/')) {
            return Violation::from(
                'Please use one backtick at the end of the reference',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
