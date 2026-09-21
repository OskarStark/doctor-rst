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

abstract class CheckListRule extends AbstractRule
{
    public string $search;
    public string $message;

    /**
     * The search pattern as plain text, when it carries no regular expression at all.
     */
    private ?string $literal = null;

    public function configure(string $pattern, ?string $message): static
    {
        $this->search = $pattern;
        $this->message = $message ?? static::getDefaultMessage();
        $this->literal = self::literal($pattern);

        return $this;
    }

    public static function getDefaultMessage(): string
    {
        return 'Please don\'t use: %s';
    }

    /**
     * @return array<string, null|string>
     */
    abstract public static function getList(): array;

    /**
     * The matched text, or null when the line does not match.
     *
     * Most entries of a check list are plain words, which `str_contains()` finds without
     * starting the regular expression engine at all. The rest goes through `preg_match()`,
     * as `AmericanEnglish` already does, rather than through `UnicodeString::match()`,
     * which installs and restores an error handler on every call.
     */
    protected function match(string $content): ?string
    {
        if (null !== $this->literal) {
            return str_contains($content, $this->literal) ? $this->literal : null;
        }

        if (1 !== preg_match($this->search.'u', $content, $matches)) {
            return null;
        }

        return $matches[0];
    }

    /**
     * A pattern is plain text when quoting it back gives the pattern itself, which rules out
     * any unescaped metacharacter. Anything else, a modifier or another delimiter included,
     * stays with the regular expression engine.
     */
    private static function literal(string $pattern): ?string
    {
        if (!str_starts_with($pattern, '/') || !str_ends_with($pattern, '/') || 2 > \strlen($pattern)) {
            return null;
        }

        $body = substr($pattern, 1, -1);
        $literal = stripcslashes($body);

        return preg_quote($literal, '/') === $body ? $literal : null;
    }
}
