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

#[Description('Ensure no non-breaking spaces or other invisible whitespace characters are used.')]
#[InvalidExample("Invalid\u{00A0}sentence")]
#[ValidExample('Valid sentence')]
final class NoNonBreakingSpace extends AbstractRule implements LineContentRule
{
    /**
     * Invalid whitespace characters:
     * - \u{00A0} - Non-breaking space
     * - \u{2002} - En space
     * - \u{2003} - Em space
     * - \u{2004} - Three-per-em space
     * - \u{2005} - Four-per-em space
     * - \u{2006} - Six-per-em space
     * - \u{2007} - Figure space
     * - \u{2008} - Punctuation space
     * - \u{2009} - Thin space
     * - \u{200A} - Hair space
     * - \u{200B} - Zero-width space
     * - \u{202F} - Narrow no-break space
     * - \u{205F} - Medium mathematical space
     * - \u{3000} - Ideographic space
     * - \u{FEFF} - Byte order mark / Zero-width no-break space.
     */
    private const string INVALID_WHITESPACE_PATTERN = '/[\x{00A0}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200A}\x{200B}\x{202F}\x{205F}\x{3000}\x{FEFF}]/u';

    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->raw()->match(self::INVALID_WHITESPACE_PATTERN)) {
            return Violation::from(
                'Please replace non-breaking or special whitespace characters with regular spaces',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
