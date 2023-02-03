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

use App\Helper\PhpHelper;
use App\Helper\TwigHelper;
use App\Helper\XmlHelper;
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Traits\ListTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Indention extends AbstractRule implements LineContentRule, Configurable
{
    use DirectiveTrait;
    use ListTrait;

    private int $size;

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
        return [RuleGroup::Experimental()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->isBlank()
            || preg_match('/([├└])/u', $line->clean()->toString())
            || $this->isPartOfListItem($lines, $number)
            || $this->isPartOfFootnote($lines, $number)
            || $this->isPartOfRstComment($lines, $number)
            || $this->isPartOfLineNumberAnnotation($lines, $number)
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
            return NullViolation::create();
        }

        $lines->seek($number);
        $indention = $line->indention();
        $minus = 0;

        $customMessage = null;

        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [
            RstParser::CODE_BLOCK_PHP,
            RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
            RstParser::CODE_BLOCK_PHP_ATTRIBUTES,
            RstParser::CODE_BLOCK_PHP_SYMFONY,
            RstParser::CODE_BLOCK_JAVASCRIPT,
            RstParser::CODE_BLOCK_SQL,
        ])) {
            if (PhpHelper::isFirstLineOfDocBlock($line)
                || PhpHelper::isFirstLineOfMultilineComment($line)
            ) {
                $minus = 0;
            } elseif ((new PhpHelper())->isPartOfMultilineComment($lines, $number)) {
                $customMessage = 'Please fix the indention of the multiline comment.';
                if (PhpHelper::isLastLineOfMultilineComment($line)
                    && $indention > 0 && 0 < $line->indention() % $this->size
                ) {
                    $minus = 1;
                } elseif ($indention > 0 && 0 < $line->indention() % $this->size) {
                    $minus = 1;
                }
            } elseif (PhpHelper::isLastLineOfDocBlock($line)
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
            && !XmlHelper::isComment($line)
            && $this->isPartOrMultilineXmlComment($lines, $number)
        ) {
            $minus = 1;
        }

        // Twig
        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [RstParser::CODE_BLOCK_TWIG, RstParser::CODE_BLOCK_HTML_TWIG])
            && !TwigHelper::isComment($line)
            && $this->isPartOrMultilineTwigComment($lines, $number)
        ) {
            $minus = 3;
        }

        if ($indention > 0 && 0 < (($indention - $minus) % $this->size)) {
            $message = $customMessage ?? sprintf('Please add %s spaces for every indention.', $this->size);

            return Violation::from(
                $message,
                $filename,
                $number + 1,
                ''
            );
        }

        return NullViolation::create();
    }

    public function isPartOrMultilineXmlComment(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (XmlHelper::isComment($lines->current(), false)) {
            return true;
        }

        $currentIndention = $lines->current()->indention();

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            $lineIndention = $lines->current()->indention();

            if ($lineIndention < $currentIndention) {
                if (XmlHelper::isComment($lines->current(), false)) {
                    return true;
                }

                return false;
            }
        }

        return false;
    }

    public function isPartOrMultilineTwigComment(Lines $lines, int $number): bool
    {
        $lines->seek($number);

        if (TwigHelper::isComment($lines->current(), false)) {
            return true;
        }

        $currentIndention = $lines->current()->indention();

        $i = $number;
        while ($i >= 1) {
            --$i;

            $lines->seek($i);

            if ($lines->current()->isBlank()) {
                continue;
            }

            $lineIndention = $lines->current()->indention();

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
