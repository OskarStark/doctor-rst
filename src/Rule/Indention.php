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
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Traits\ListTrait;
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
        return [Registry::GROUP_SONATA, Registry::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);

        if (RstParser::isBlankLine($lines->current())
            || preg_match('/(├|└)/', RstParser::clean($lines->current()))
            || $this->isPartOfListItem($lines, $number)
            || $this->in(RstParser::DIRECTIVE_INDEX, $lines, $number)
            || $this->in(RstParser::DIRECTIVE_FIGURE, $lines, $number)
            || $this->in(RstParser::DIRECTIVE_IMAGE, $lines, $number)
            || $this->in(RstParser::DIRECTIVE_TOCTREE, $lines, $number)
        ) {
            return;
        }

        $indention = RstParser::indention($lines->current());

        $minus = 0;
        if (preg_match('/^\*/', RstParser::clean($lines->current()))
            && $this->in(RstParser::DIRECTIVE_CODE_BLOCK, $lines, $number, [
                RstParser::CODE_BLOCK_PHP,
                RstParser::CODE_BLOCK_PHP_ANNOTATIONS,
                RstParser::CODE_BLOCK_JAVASCRIPT,
                RstParser::CODE_BLOCK_SQL,
            ])
        ) {
            $minus = 1;
        }

        if ($indention > 0 && 0 !== (($indention % $this->size) - $minus)) {
            return sprintf('Please add %s spaces for every indention.', $this->size);
        }
    }
}
