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
use App\Handler\Registry;
use App\Helper\PhpHelper;
use App\Value\RuleGroup;

/**
 * @Description("Ensures to have 2 backslashes when highlighting a namespace to have valid output.")
 * @ValidExample("``App\Entity\Foo``")
 * @ValidExample("`App\\Entity\\Foo`")
 * @InvalidExample("``App\\Entity\\Foo``")
 * @InvalidExample("`App\Entity\Foo`")
 */
class ValidInlineHighlightedNamespaces extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        // check 2 backticks
        if (preg_match_all('/(``[^`]+``)/', $line, $occurences, PREG_SET_ORDER)) {
            foreach (array_unique($occurences[0]) as $occurence) {
                if (!PhpHelper::containsBackslash($occurence)) {
                    continue;
                }

                if (PhpHelper::isUsingTwoBackslashes($lala = str_replace('``', '', $occurence))) {
                    return sprintf('Please use 1 backslash when highlighting a namespace with double backticks: %s', $occurence);
                }
            }

            goto end;
        }

        if (preg_match_all('/(`[^`]+`)/', $line, $occurences, PREG_SET_ORDER)) { // check 1 backtick
            foreach (array_unique($occurences[0]) as $occurence) {
                if (!PhpHelper::containsBackslash($occurence)) {
                    continue;
                }

                if (!PhpHelper::isUsingTwoBackslashes(str_replace('`', '', $occurence))) {
                    return sprintf('Please use 2 backslashes when highlighting a namespace with single backticks: %s', $occurence);
                }
            }

            goto end;
        }

        end:
    }
}
