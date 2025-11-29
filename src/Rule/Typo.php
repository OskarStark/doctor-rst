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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Report common typos.')]
final class Typo extends CheckListRule implements LineContentRule
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

        if ($matches = $line->raw()->match($this->search)) {
            /** @var string[] $matches */
            return Violation::from(
                \sprintf($this->message, $matches[0]),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }

    public static function getDefaultMessage(): string
    {
        return 'Typo in word "%s"';
    }

    /**
     * @return array<string, null|string>
     */
    public static function getList(): array
    {
        return [
            '/compsoer/i' => null,
            '/registerbundles\(\)/' => 'Typo in word "%s", use "registerBundles()"',
            '/retun/' => null,
            '/displayes/i' => null,
            '/mantains/i' => null,
            '/doctine/i' => null,
            '/adress/i' => null,
            '/argon21/' => 'Typo in word "%s", use "argon2i"',
            '/descritpion/i' => null,
            '/recalcuate/i' => null,
            '/achived/i' => null,
            '/overriden/i' => null,
            '/succesfully/i' => null,
            '/optionnally/i' => null,
            '/esimated/i' => null,
            '/strengh/i' => null,
            '/mehtod/i' => null,
            '/contraint/i' => null,
            '/instanciation/i' => 'Typo in word "%s", use "instantiation"',
        ];
    }
}
