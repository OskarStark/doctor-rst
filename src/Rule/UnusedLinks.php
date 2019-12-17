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
use App\Handler\Registry;
use App\Rst\RstParser;
use App\Rst\Value\LinkDefinition;
use App\Rst\Value\LinkUsage;
use App\Value\Lines;
use App\Value\RuleGroup;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @Description("Report all links which are not used in the file anymore.")
 */
class UnusedLinks extends AbstractRule implements Rule, ResetInterface
{
    /** @var LinkUsage[] */
    private $linkUsages = [];

    /** @var LinkDefinition[] */
    private $linkDefinitions = [];

    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines = $lines->toIterator();

        $lines->seek($number);

        while ($lines->valid()) {
            if (RstParser::isLinkDefinition($lines->current())) {
                $definition = LinkDefinition::fromLine($lines->current());
                $this->linkDefinitions[$definition->name()->value()] = $definition;
            }

            preg_match_all('/`([^`]+)`_/', $lines->current(), $matches);
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    if (RstParser::isLinkUsage($match)) {
                        $usage = LinkUsage::fromLine($match);
                        $this->linkUsages[$usage->name()->value()] = $usage;
                    }
                }
            }

            $lines->next();
        }

        foreach ($this->linkDefinitions as $definition) {
            if (isset($this->linkUsages[$definition->name()->value()])) {
                unset($this->linkDefinitions[$definition->name()->value()]);
            }
        }

        if (!empty($this->linkDefinitions)) {
            return sprintf(
                'The following link definitions aren\'t used anymore and should be removed: %s',
                implode(', ', array_unique(array_keys($this->linkDefinitions)))
            );
        }

        return null;
    }

    public function reset(): void
    {
        $this->linkUsages = [];
        $this->linkDefinitions = [];
    }

    public static function getType(): int
    {
        return Rule::TYPE_FILE;
    }
}
