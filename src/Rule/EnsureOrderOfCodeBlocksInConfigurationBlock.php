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

use App\Handler\RulesHandler;
use App\Rst\RstParser;
use Webmozart\Assert\Assert;

class EnsureOrderOfCodeBlocksInConfigurationBlock extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
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

        $codeBlocks = [];
        while ($lines->valid() && ($indention < RstParser::indention($lines->current()) || RstParser::isBlankLine($lines->current()))) {
            if (RstParser::directiveIs($lines->current(), RstParser::DIRECTIVE_CODE_BLOCK)) {
                $codeBlocks[] = RstParser::clean($lines->current());
            }

            $lines->next();
        }

        foreach ($codeBlocks as $key => $codeBlock) {
            if (!\in_array($codeBlock, self::getValidOrder())) {
                unset($codeBlocks[$key]);
            }
        }

        $validOrder = self::getValidOrder();
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
                str_replace('.. code-block:: ', '', implode(', ', self::getValidOrder()))
            );
        }
    }

    private static function getValidOrder(): array
    {
        return [
            '.. code-block:: php-annotations',
            '.. code-block:: yaml',
            '.. code-block:: xml',
            '.. code-block:: php',
        ];
    }
}
