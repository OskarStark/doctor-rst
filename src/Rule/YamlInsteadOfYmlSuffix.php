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
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

/**
 * @Description("Make sure to only use `yaml` instead of `yml`.")
 *
 * @ValidExample({".travis.yml", "..code-block:: yaml", "Please add this to your services.yaml file."})
 *
 * @InvalidExample({"..code-block:: yml", "Please add this to your services.yml file."})
 */
class YamlInsteadOfYmlSuffix extends AbstractRule implements LineContentRule
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
            $message = 'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"';

            return Violation::from(
                $message,
                $filename,
                1,
                ''
            );
        }

        if ($matches = $line->raw()->match('/\.yml/i')) {
            $message = sprintf('Please use ".yaml" instead of "%s"', $matches[0]);

            return Violation::from(
                $message,
                $filename,
                1,
                ''
            );
        }

        return NullViolation::create();
    }
}
