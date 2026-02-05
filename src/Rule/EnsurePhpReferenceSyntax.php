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
use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\u;

#[Description('Ensure php reference syntax is valid.')]
#[InvalidExample('The :class:`Symfony\\Component\\Notifier\\Transport`` class')]
#[InvalidExample('The :class:`Symfony\\\\AI\\\\Platform\\PlatformInterface` class')]
#[ValidExample('The :class:`Symfony\\Component\\Notifier\\Transport` class')]
final class EnsurePhpReferenceSyntax extends AbstractRule implements LineContentRule
{
    private const string PATTERN = '/:(class|method|namespace|phpclass|phpfunction|phpmethod):`([^`]+)`/';

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

        // Check for inconsistent backslash usage
        if (preg_match_all(self::PATTERN, $line->raw()->toString(), $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $reference = u($match[2]);

                if (self::hasInconsistentBackslashes($reference)) {
                    return Violation::from(
                        \sprintf('Please use consistent backslash escaping in PHP reference: `%s`', $reference->toString()),
                        $filename,
                        $number + 1,
                        $line,
                    );
                }
            }
        }

        return NullViolation::create();
    }

    /**
     * Check if a reference has inconsistent backslash escaping.
     * In RST, namespaces should use consistent double backslashes (\\).
     * This detects cases like "Symfony\\AI\\Platform\PlatformInterface"
     * where there's a mix of \\ and \.
     */
    private static function hasInconsistentBackslashes(UnicodeString $reference): bool
    {
        // No backslash at all - consistent
        if (!$reference->containsAny('\\')) {
            return false;
        }

        // Replace double backslashes with a placeholder
        $withoutDouble = $reference->replace('\\\\', "\x00");

        // If there are still single backslashes after removing doubles, it's inconsistent
        // But we need to make sure there were also double backslashes originally
        $hasDoubleBackslashes = $reference->containsAny('\\\\');
        $hasSingleBackslashesAfterRemovingDoubles = $withoutDouble->containsAny('\\');

        return $hasDoubleBackslashes && $hasSingleBackslashesAfterRemovingDoubles;
    }
}
