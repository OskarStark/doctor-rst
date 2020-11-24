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
use App\Rst\Value\LinkDefinition;
use App\Rst\Value\LinkUsage;
use App\Value\Lines;
use App\Value\RuleGroup;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @Description("Report all links which are defined, but not used in the file anymore.")
 */
class UnusedLinks extends AbstractRule implements FileContentRule, ResetInterface
{
    /** @var LinkUsage[] */
    private array $linkUsages = [];

    /** @var LinkDefinition[] */
    private array $linkDefinitions = [];

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines): ?string
    {
        /* @todo this should not be needed, make sure its always at position 0 */
        $lines->seek(0);

        while ($lines->valid()) {
            if (RstParser::isLinkDefinition($lines->current())) {
                $definition = LinkDefinition::fromLine($lines->current()->raw()->toString());
                $this->linkDefinitions[$definition->name()->value()] = $definition;
            }

            preg_match_all('/(?:`[^`]+`|(?:(?!_)\w)+(?:[-._+:](?:(?!_)\w)+)*+)_/', $lines->current()->raw()->toString(), $matches);
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
                'The following link definitions aren\'t used anymore and should be removed: "%s"',
                implode('", "', array_unique(array_keys($this->linkDefinitions)))
            );
        }

        return null;
    }

    public function reset(): void
    {
        $this->linkUsages = [];
        $this->linkDefinitions = [];
    }
}
