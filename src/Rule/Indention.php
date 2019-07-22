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
use App\Helper\PhpHelper;
use App\Helper\TwigHelper;
use App\Helper\XmlHelper;
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Traits\ListTrait;
use App\Value\RuleGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Indention extends AbstractRule implements Rule, Configurable
{
    use DirectiveTrait;
    use ListTrait;

    /** @var int */
    private $size;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setDefault('size', 4)
            ->setRequired('size')
            ->setAllowedTypes('size', 'int')
        ;

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        $this->size = $resolvedOptions['size'];
    }

    public static function getGroups(): array
    {
        return [RuleGroup::fromString(Registry::GROUP_EXPERIMENTAL)];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);

        if (RstParser::isBlankLine($lines->current())
            || preg_match('/(├|└)/', RstParser::clean($lines->current()))
            || $this->isPartOfListItem($lines, $number)
            || $this->isPartOfFootnote($lines, $number)
            || $this->isPartOfRstComment($lines, $number)
            || $this->in(RstParser::DIRECTIVE_INDEX, $lines, $number)
            || $this->in(RstParser::DIRECTIVE_FIGURE, $lines, $number)
            || $this->in(RstParser::DIRECTIVE_IMAGE, $lines, $number)
            || $this->in(RstParser::DIRECTIVE_TOCTREE, $lines, $number)
            || $this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [
                RstParser::CODE_BLOCK_TEXT,
                RstParser::CODE_BLOCK_TERMINAL,
                RstParser::CODE_BLOCK_BASH,
                RstParser::CODE_BLOCK_SQL,
            ])
        ) {
            return;
        }

        $indention = RstParser::indention($lines->current());
        $minus = 0;

        $customMessage = null;

        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [
            RstParser::CODE_BLOCK_PHP,
            RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
            RstParser::CODE_BLOCK_JAVASCRIPT,
            RstParser::CODE_BLOCK_SQL,
        ])) {
            if (PhpHelper::isFirstLineOfDocBlock($lines->current())
                || PhpHelper::isFirstLineOfMultilineComment($lines->current())
            ) {
                $minus = 0;
            } elseif ((new PhpHelper())->isPartOfMultilineComment($lines, $number)) {
                $customMessage = 'Please fix the indention of the multiline comment.';
                if (PhpHelper::isLastLineOfMultilineComment($lines->current())
                    && $indention > 0 && 0 < RstParser::indention($lines->current()) % $this->size
                ) {
                    $minus = 1;
                } elseif ($indention > 0 && 0 < RstParser::indention($lines->current()) % $this->size) {
                    $minus = 1;
                }
            } elseif (PhpHelper::isLastLineOfDocBlock($lines->current())
                && (new PhpHelper())->isPartOfDocBlock($lines, $number)
            ) {
                $customMessage = 'Please fix the indention of the PHP DocBlock.';
                $minus = 1;
            } elseif ((new PhpHelper())->isPartOfDocBlock($lines, $number)) {
                $customMessage = 'Please fix the indention of the PHP DocBlock.';
                $minus = 1;
            }
        }

        // XML
        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [RstParser::CODE_BLOCK_XML])
            && !XmlHelper::isComment($lines->current())
            && $this->isPartOrMultilineXmlComment($lines, $number)
        ) {
            $minus = 1;
        }

        // Twig
        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [RstParser::CODE_BLOCK_TWIG, RstParser::CODE_BLOCK_HTML_TWIG])
            && !TwigHelper::isComment($lines->current())
            && $this->isPartOrMultilineTwigComment($lines, $number)
        ) {
            $minus = 3;
        }

        if ($indention > 0 && 0 < (($indention - $minus) % $this->size)) {
            return $customMessage ?? sprintf('Please add %s spaces for every indention.', $this->size);
        }
    }

    public function isPartOrMultilineXmlComment(\ArrayIterator $lines, int $number): bool
    {
        $lines = Helper::cloneIterator($lines, $number);

        if (XmlHelper::isComment($lines->current(), false)) {
            return true;
        }

        $currentIndention = RstParser::indention($lines->current());

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if (RstParser::isBlankLine($lines->current())) {
                continue;
            }

            $lineIndention = RstParser::indention($lines->current());

            if ($lineIndention < $currentIndention) {
                if (XmlHelper::isComment($lines->current(), false)) {
                    return true;
                }

                return false;
            }
        }

        return false;
    }

    public function isPartOrMultilineTwigComment(\ArrayIterator $lines, int $number): bool
    {
        $lines = Helper::cloneIterator($lines, $number);

        if (TwigHelper::isComment($lines->current(), false)) {
            return true;
        }

        $currentIndention = RstParser::indention($lines->current());

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if (RstParser::isBlankLine($lines->current())) {
                continue;
            }

            $lineIndention = RstParser::indention($lines->current());

            if ($lineIndention < $currentIndention) {
                if (TwigHelper::isComment($lines->current(), false)) {
                    return true;
                }

                return false;
            }
        }

        return false;
    }
}
