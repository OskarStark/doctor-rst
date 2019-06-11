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

use App\Handler\Registry;
use App\Helper\Helper;
use App\Rst\RstParser;
use App\Value\RuleGroup;
use Webmozart\Assert\Assert;

class EnsureOrderOfCodeBlocksInConfigurationBlock extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CONFIGURATION_BLOCK)) {
            return;
        }

        $indention = RstParser::indention($line);

        $lines->next();

        $validOrder = self::validOrder();

        $codeBlocks = [];
        while ($lines->valid() && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))) {
            if (RstParser::directiveIs($lines->current(), RstParser::DIRECTIVE_CODE_BLOCK)) {
                $codeBlocks[] = RstParser::clean($lines->current());

                // if its an xml code-block, check if it contains xliff
                // @todo refactor in extra method: getDirectiveContent
                if (RstParser::codeBlockDirectiveIsTypeOf($lines->current(), RstParser::CODE_BLOCK_XML)) {
                    $content = Helper::cloneIterator($lines, (int) $lines->key());
                    $content->next();

                    while ($content->valid() && (RstParser::isBlankLine($content->current()) || RstParser::indention($lines->current()) < RstParser::indention($content->current()))) {
                        if (preg_match('/xliff/', $content->current())) {
                            $validOrder = self::validOrderIncludingXliff();

                            break;
                        }

                        $content->next();
                    }
                }
            }

            $lines->next();
        }

        foreach ($codeBlocks as $key => $codeBlock) {
            if (!\in_array($codeBlock, $validOrder)) {
                unset($codeBlocks[$key]);
            }
        }

        foreach ($validOrder as $key => $order) {
            if (!\in_array($order, $codeBlocks)) {
                unset($validOrder[$key]);
            }
        }

        try {
            Assert::eq(array_values($codeBlocks), array_values($validOrder));
        } catch (\InvalidArgumentException $e) {
            return sprintf(
                'Please use the following order for your code blocks: "%s"',
                str_replace('.. code-block:: ', '', implode(', ', $validOrder))
            );
        }
    }

    private static function validOrder(): array
    {
        return [
            '.. code-block:: php-annotations',
            '.. code-block:: yaml',
            '.. code-block:: xml',
            '.. code-block:: php',
        ];
    }

    private static function validOrderIncludingXliff(): array
    {
        return [
            '.. code-block:: xml',
            '.. code-block:: php-annotations',
            '.. code-block:: yaml',
            '.. code-block:: php',
        ];
    }
}
