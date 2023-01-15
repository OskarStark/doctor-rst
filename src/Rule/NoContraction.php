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
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure contractions are not used.")
 * @InvalidExample("It's an example")
 * @ValidExample("It is an example")
 */
class NoContraction extends CheckListRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match($this->search, $line->raw()->toString(), $matches)) {
            return sprintf($this->message, $matches[0]);
        }

        return null;
    }

    public static function getDefaultMessage(): string
    {
        return 'Please do not use contraction for: %s';
    }

    /**
     * @return array<string, null>
     */
    public static function getList(): array
    {
        return [
            '/i\'m/i' => null,
            '/(you|we|they)\'re/i' => null,
            '/(he|she|it)\'s/i' => null,
            '/(you|we|they)\'ve/i' => null,
            '/(i|you|he|she|it|we|they)\'ll/i' => null,
            '/(i|you|he|she|it|we|they)\'d/i' => null,
            '/(aren|can|couldn|didn|hasn|haven|isn|mustn|shan|shouldn|wasn|weren|won|wouldn)\'t/i' => null,
        ];
    }
}
