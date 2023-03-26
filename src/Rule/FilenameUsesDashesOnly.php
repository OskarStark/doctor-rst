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

use App\Annotations\Rule\Description;
use App\Annotations\Rule\InvalidExample;
use App\Annotations\Rule\ValidExample;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;
use function Symfony\Component\String\u;

/**
 * @Description("Ensures a filename uses only dashes (`-`), but are allowed to start with underscore (`_`). It is a common practice to prefix included files with underscores (`_`).")
 *
 * @InvalidExample("custom_extensions.rst")
 *
 * @ValidExample({"custom-extensions.rst", "_custom-extensions.rst"})
 */
final class FilenameUsesDashesOnly extends AbstractRule implements FileInfoRule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(\SplFileInfo $fileInfo): ViolationInterface
    {
        $filename = u($fileInfo->getFilename());

        /*
         * Often includes are prefixed with "_" like:
         *   _custom-extensions.rst
         */
        if ($filename->truncate(1)->equalsTo('_')) {
            $filename = $filename->slice(1)->toUnicodeString();
        }

        if ($filename->containsAny('_')) {
            $message = sprintf(
                'Please use dashes (-) for the filename: %s',
                $fileInfo->getFilename(),
            );

            return Violation::from(
                $message,
                $fileInfo->getFilename(),
                1,
                '',
            );
        }

        return NullViolation::create();
    }
}
