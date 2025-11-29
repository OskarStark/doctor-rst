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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @no-named-arguments
 */
#[Description('Make sure to only use `yaml` instead of `yml`.')]
#[ValidExample('.travis.yml')]
#[ValidExample('..code-block:: yaml')]
#[ValidExample('Please add this to your services.yaml file.')]
#[InvalidExample('..code-block:: yml')]
#[InvalidExample('Please add this to your services.yml file.')]
final class YamlInsteadOfYmlSuffix extends AbstractRule implements LineContentRule
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

        if ($line->raw()->match('/\.travis\.yml/')) {
            return NullViolation::create();
        }

        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_YML)) {
            return Violation::from(
                'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"',
                $filename,
                $number + 1,
                $line,
            );
        }

        if ($matches = $line->raw()->match('/\.yml/i')) {
            /** @var string[] $matches */
            return Violation::from(
                \sprintf('Please use ".yaml" instead of "%s"', $matches[0]),
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
