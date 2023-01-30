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
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Propose alternatives for disallowed code block types.")
 */
class ReplaceCodeBlockTypes extends CheckListRule implements LineContentRule
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

        if (RstParser::codeBlockDirectiveIsTypeOf($line, $this->search, true)) {
            $message = $this->message;

            return Violation::from(
                $message,
                $filename,
                1,
                ''
            );
        }

        return NullViolation::create();
    }

    public static function getDefaultMessage(): string
    {
        return 'Please do not use type "%s" for code-block.';
    }

    /**
     * @return array<string, string>
     */
    public static function getList(): array
    {
        $replacements = [
            RstParser::CODE_BLOCK_JINJA => RstParser::CODE_BLOCK_TWIG,
            RstParser::CODE_BLOCK_HTML_JINJA => RstParser::CODE_BLOCK_HTML_TWIG,
            RstParser::CODE_BLOCK_JS => RstParser::CODE_BLOCK_JAVASCRIPT,
        ];

        $list = [];
        foreach ($replacements as $current => $new) {
            $list[$current] = sprintf('Please do not use type "%s" for code-block, use "%s" instead', $current, $new);
        }

        return $list;
    }
}
