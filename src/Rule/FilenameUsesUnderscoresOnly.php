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
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

use function Symfony\Component\String\u;

/**
 * @Description("Ensures a filename uses only underscores (`_`).")
 *
 * @InvalidExample("custom-extensions.rst")
 *
 * @ValidExample("custom_extensions.rst")
 * @ValidExample("_custom_extensions.rst")
 */
final class FilenameUsesUnderscoresOnly extends AbstractRule implements FileInfoRule
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

        if ($filename->containsAny('-')) {
            $message = sprintf(
                'Please use underscores (_) for the filename: %s',
                $fileInfo->getFilename()
            );

            return Violation::from(
                $message,
                $fileInfo->getFilename(),
                1,
                ''
            );
        }

        return NullViolation::create();
    }
}
