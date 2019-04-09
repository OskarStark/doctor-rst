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
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotManyBlankLines extends AbstractRule implements Rule, Configurable
{
    /** @var int */
    private $max;

    public function getConfiguration(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefault('max', 2)
            ->setRequired('max')
            ->setAllowedTypes('max', 'int')
        ;

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->getConfiguration();

        $resolvedOptions = $resolver->resolve($options);

        $this->max = $resolvedOptions['max'];
    }

    public static function runOnlyOnBlankline(): bool
    {
        return true;
    }

    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);

        if (!RstParser::isBlankLine($lines->current())) {
            return;
        }

        $blanklines = 1;
        $lines->next();

        while ($lines->valid() && RstParser::isBlankLine($lines->current())) {
            ++$blanklines;

            $lines->next();
        }

        if ($blanklines > $this->max) {
            return sprintf('Please use max %s blank lines, you used %s', $this->max, $blanklines);
        }
    }
}
