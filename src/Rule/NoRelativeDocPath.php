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

#[Description('Ensure :doc: directives use absolute paths.')]
#[InvalidExample(':doc:`maintenance`')]
#[InvalidExample(':doc:`File <maintenance>`')]
#[ValidExample(':doc:`/contributing/code/maintenance`')]
#[ValidExample(':doc:`File </contributing/code/maintenance>`')]
final class NoRelativeDocPath extends AbstractRule implements LineContentRule
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

        // Match :doc: with label and link: :doc:`Label <path>`
        if ($matches = $line->raw()->match('/:doc:`(?P<label>[^<`]+)<(?P<link>[^>]+)>`/')) {
            /** @var array{label: string, link: string} $matches */
            $link = trim($matches['link']);

            if (!str_starts_with($link, '/')) {
                return Violation::from(
                    \sprintf(
                        'Please use an absolute path for :doc: directive, e.g., :doc:`/%s`',
                        $link,
                    ),
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        // Match :doc: with only path: :doc:`path`
        if ($matches = $line->raw()->match('/:doc:`(?P<path>[^<>`]+)`/')) {
            /** @var array{path: string} $matches */
            $path = trim($matches['path']);

            if (!str_starts_with($path, '/')) {
                return Violation::from(
                    \sprintf(
                        'Please use an absolute path for :doc: directive, e.g., :doc:`/%s`',
                        $path,
                    ),
                    $filename,
                    $number + 1,
                    $line,
                );
            }
        }

        return NullViolation::create();
    }
}
