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
use App\Attribute\Rule\InvalidExample;
use App\Attribute\Rule\ValidExample;
use App\Rst\RstParser;
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Description('Make sure configured syntax elements are not used alone on a line, prefer the full declaration.')]
#[InvalidExample(<<<'RST'
The following example shows the usage:

::

    $uuidFactory = new UuidFactory();
RST)]
#[ValidExample(<<<'RST'
The following example shows the usage:

.. code-block:: php

    $uuidFactory = new UuidFactory();
RST)]
final class NoStandaloneSyntaxElement extends AbstractRule implements Configurable, LineContentRule
{
    use DirectiveTrait;

    /**
     * @var string[]
     */
    private array $elements;

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setDefault('elements', ['::'])
            ->setRequired('elements')
            ->setAllowedTypes('elements', 'string[]');

        return $resolver;
    }

    public function setOptions(array $options): void
    {
        $resolver = $this->configureOptions(new OptionsResolver());

        $resolvedOptions = $resolver->resolve($options);

        /** @phpstan-ignore-next-line */
        $this->elements = $resolvedOptions['elements'];
    }

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

        if ($line->isBlank()) {
            return NullViolation::create();
        }

        $element = $line->clean()->toString();

        if (!\in_array($element, $this->elements, true)) {
            return NullViolation::create();
        }

        // the element may legitimately be part of the code being shown
        if ($this->in(RstParser::DIRECTIVE_CODE_BLOCK, clone $lines, $number)) {
            return NullViolation::create();
        }

        return Violation::from(
            \sprintf('Please do not use "%s" alone on a line, prefer the full declaration.', $element),
            $filename,
            $number + 1,
            $line,
        );
    }
}
