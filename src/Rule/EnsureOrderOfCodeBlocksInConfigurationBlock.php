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

use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Webmozart\Assert\Assert;

class EnsureOrderOfCodeBlocksInConfigurationBlock extends AbstractRule implements LineContentRule
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

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CONFIGURATION_BLOCK)) {
            return NullViolation::create();
        }

        $indention = $line->indention();

        $lines->next();

        $validOrder = self::validOrder();
        $validXliffOrder = self::validOrderIncludingXliff();
        $validOrderOnlyPhpSymfonyAndPhpStandalone = self::validOrderOnlyPhpSymfonyAndPhpStandalone();

        $xliff = false;
        $phpSymfony = false;
        $phpStandalone = false;

        $codeBlocks = [];
        while ($lines->valid() && ($indention < $lines->current()->indention() || $lines->current()->isBlank())) {
            if (RstParser::directiveIs($lines->current(), RstParser::DIRECTIVE_CODE_BLOCK)) {
                if ($lines->current()->raw()->endsWith(RstParser::CODE_BLOCK_PHP_SYMFONY)) {
                    $phpSymfony = true;
                }

                if ($lines->current()->raw()->endsWith(RstParser::CODE_BLOCK_PHP_STANDALONE)) {
                    $phpStandalone = true;
                }

                $codeBlocks[] = $lines->current()->clean()->toString();

                // if its an xml code-block, check if it contains xliff
                // @todo refactor in extra method: getDirectiveContent
                if (RstParser::codeBlockDirectiveIsTypeOf($lines->current(), RstParser::CODE_BLOCK_XML)) {
                    $content = clone $lines;
                    $content->seek($lines->key() + 1);

                    while ($content->valid() && ($content->current()->isBlank() || $lines->current()->indention() < $content->current()->indention())) {
                        if (false !== strpos($content->current()->raw()->toString(), 'xliff')) {
                            $xliff = true;

                            break;
                        }

                        $content->next();
                    }
                }
            }

            $lines->next();
        }

        $onlyPhpSymfonyAndPhpStandalone = false;
        if ($phpSymfony && $phpStandalone && 2 === \count($codeBlocks)) {
            $onlyPhpSymfonyAndPhpStandalone = true;
        }

        foreach ($codeBlocks as $key => $codeBlock) {
            if (!\in_array($codeBlock, $validOrder, true)) {
                unset($codeBlocks[$key]);
            }
        }

        foreach ($validOrder as $key => $order) {
            if (!\in_array($order, $codeBlocks, true)) {
                unset($validOrder[$key]);
            }
        }

        // only php-symfony and php-standalone
        if ($onlyPhpSymfonyAndPhpStandalone
            && !$this->equal($codeBlocks, $validOrderOnlyPhpSymfonyAndPhpStandalone)
        ) {
            $message = sprintf(
                'Please use the following order for your code blocks: "%s"',
                str_replace('.. code-block:: ', '', implode(', ', $validOrderOnlyPhpSymfonyAndPhpStandalone))
            );

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                ''
            );
        }

        // no xliff
        if (!$xliff && !$this->equal($codeBlocks, $validOrder) && 1 !== \count($validOrder)) {
            $message = sprintf(
                'Please use the following order for your code blocks: "%s"',
                str_replace('.. code-block:: ', '', implode(', ', $validOrder))
            );

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                ''
            );
        }

        // xliff
        foreach ($validXliffOrder as $key => $order) {
            if (!\in_array($order, $codeBlocks, true)) {
                unset($validXliffOrder[$key]);
            }
        }

        if ($xliff && !$this->equal($codeBlocks, $validXliffOrder) && !$this->equal($codeBlocks, $validOrder)) {
            $message = sprintf(
                'Please use the following order for your code blocks: "%s"',
                str_replace('.. code-block:: ', '', implode(', ', $validXliffOrder))
            );

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                ''
            );
        }

        return NullViolation::create();
    }

    public function equal(array $codeBlocks, array $validOrder): bool
    {
        try {
            Assert::eq(array_values($codeBlocks), array_values($validOrder));

            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    private static function validOrder(): array
    {
        return [
            '.. code-block:: php-symfony',
            '.. code-block:: php-annotations',
            '.. code-block:: php-attributes',
            '.. code-block:: yaml',
            '.. code-block:: xml',
            '.. code-block:: php',
            '.. code-block:: php-standalone',
        ];
    }

    private static function validOrderIncludingXliff(): array
    {
        return [
            '.. code-block:: xml',
            '.. code-block:: php-annotations',
            '.. code-block:: php-attributes',
            '.. code-block:: yaml',
            '.. code-block:: php',
        ];
    }

    private static function validOrderOnlyPhpSymfonyAndPhpStandalone(): array
    {
        return [
            '.. code-block:: php-symfony',
            '.. code-block:: php-standalone',
        ];
    }
}
