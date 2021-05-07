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
use App\Rst\RstParser;
use App\Rst\Value\LinkDefinition;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure link definition contains valid link.")
 * @InvalidExample(".. _DOCtor-RST: htt//github.com/OskarStark/DOCtor-RST")
 * @ValidExample(".. _DOCtor-RST: https://github.com/OskarStark/DOCtor-RST")
 */
class EnsureLinkDefinitionContainsValidUrl extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->isBlank()
            || !RstParser::isLinkDefinition($line)
        ) {
            return null;
        }

        $linkDefinition = LinkDefinition::fromLine($line->raw()->toString());

        $parsed = parse_url($linkDefinition->url()->value());

        if (\is_array($parsed)
            && isset($parsed['scheme'])
            && \in_array($parsed['scheme'], ['http', 'https'])
            && isset($parsed['host'])
        ) {
            return null;
        }

        return sprintf(
            'Invalid url in "%s"',
            $line->clean()->toString()
        );
    }
}
