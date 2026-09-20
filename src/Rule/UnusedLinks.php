<?php

declare(strict_types=1);

/**
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use App\Attribute\Rule\Description;
use App\Rst\RstParser;
use App\Rst\Value\LinkDefinition;
use App\Rst\Value\LinkUsage;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Contracts\Service\ResetInterface;

#[Description('Report all links which are defined, but not used in the file anymore.')]
final class UnusedLinks extends AbstractRule implements FileContentRule, ResetInterface
{
    /**
     * @var LinkUsage[]
     */
    private array $linkUsages = [];

    /**
     * @var LinkDefinition[]
     */
    private array $linkDefinitions = [];

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, string $filename): ViolationInterface
    {
        $block = [];

        while ($lines->valid()) {
            $line = $lines->current();

            if (RstParser::isLinkDefinition($line)) {
                $definition = LinkDefinition::fromLine($line->raw()->toString());
                $this->linkDefinitions[$definition->name()->value()] = $definition;

                $this->collectLinkUsages($block);
                $block = [];
            } elseif ($line->isBlank()) {
                $this->collectLinkUsages($block);
                $block = [];
            } else {
                $block[] = $line->raw()->toString();
            }

            $lines->next();
        }

        $this->collectLinkUsages($block);

        foreach ($this->linkDefinitions as $definition) {
            if (isset($this->linkUsages[$definition->name()->value()])) {
                unset($this->linkDefinitions[$definition->name()->value()]);
            }
        }

        if ([] !== $this->linkDefinitions) {
            $message = \sprintf(
                'The following link definitions aren\'t used anymore and should be removed: "%s"',
                implode('", "', array_unique(array_keys($this->linkDefinitions))),
            );

            return Violation::from(
                $message,
                $filename,
                1,
                '',
            );
        }

        return NullViolation::create();
    }

    public function reset(): void
    {
        $this->linkUsages = [];
        $this->linkDefinitions = [];
    }

    /**
     * A link usage may span several lines, so it is searched in the whole block instead of
     * line by line. Blocks end at a blank line or at a link definition, which a link usage
     * cannot span.
     *
     * Inline literals are dropped first: they hold no markup, and their backticks would
     * otherwise pair with the ones of a real link usage.
     *
     * @param string[] $block
     */
    private function collectLinkUsages(array $block): void
    {
        if ([] === $block) {
            return;
        }

        $content = (string) preg_replace('/``.+?``/s', '', implode("\n", $block));

        preg_match_all(
            '/(?:`[^`]+`|(?:(?!_)\w)+(?:[-._+:](?:(?!_)\w)+)*+)_/',
            $content,
            $matches,
        );

        foreach ($matches[0] as $match) {
            if (RstParser::isLinkUsage($match)) {
                $usage = LinkUsage::fromLine($match);
                $this->linkUsages[$usage->name()->value()] = $usage;
            }
        }
    }
}
