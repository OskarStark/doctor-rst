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
use function Symfony\Component\String\u;

/**
 * @no-named-arguments
 */
#[Description('Ensure a space between label and link in :doc: directive.')]
#[InvalidExample(':doc:`File</reference/constraints/File>`')]
#[ValidExample(':doc:`File </reference/constraints/File>`')]
class SpaceBetweenLabelAndLinkInDoc extends AbstractRule implements LineContentRule
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

        if ($matches = $line->raw()->match('/:doc:`(?P<label>.*)<(?P<link>.*)>`/')) {
            /** @var array{label: string, link: string} $matches */
            if (!u($matches['label'])->endsWith(' ')) {
                $message = \sprintf(
                    'Please add a space between "%s" and "<%s>" inside :doc: directive',
                    $matches['label'],
                    $matches['link'],
                );

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
}
