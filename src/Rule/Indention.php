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
use Symfony\Component\OptionsResolver\OptionsResolver;

class Indention extends AbstractRule implements Rule, Configurable
{
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

    public static function getType(): int
    {
        return Rule::TYPE_FILE;
    }

    public static function getGroups(): array
    {
        return [Registry::GROUP_SONATA, Registry::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);

        $initial = RstParser::indention($lines->current());

        if (0 !== $initial) {
            return 'A file should start without any indention.';
        }

        while ($lines->valid()) {
            if (RstParser::isBlankLine($lines->current())) {
                $lines->next();

                continue;
            }

            $indention = RstParser::indention($lines->current());

            if (0 !== ($indention % $this->size)) {
                return sprintf('Please add %s spaces for every indention.', $this->size);
            }

            $lines->next();
        }
    }
}
