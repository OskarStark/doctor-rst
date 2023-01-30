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

use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

class Replacement extends CheckListRule implements LineContentRule
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

        if ($matches = $line->clean()->match($this->search)) {
            $message = sprintf($this->message, $matches[0]);

            return Violation::from(
                $message,
                $filename,
                1,
                ''
            );
        }

        return NullViolation::create();
    }

    public static function getDefaultMessage(): string
    {
        return 'Please don\'t use: %s';
    }

    /**
     * @return array<string, string>
     */
    public static function getList(): array
    {
        return [
            '/^([\s]+)?\/\/.\.(\.)?$/' => 'Please replace "%s" with "// ..."',
            '/^([\s]+)?#.\.(\.)?$/' => 'Please replace "%s" with "# ..."',
            '/^([\s]+)?<!--(.\.(\.)?|[\s]+\.\.[\s]+)-->$/' => 'Please replace "%s" with "<!-- ... -->"',
            '/^([\s]+)?{#(.\.(\.)?|[\s]+\.\.[\s]+)#}$/' => 'Please replace "%s" with "{# ... #}"',
            '/apps/' => 'Please replace "%s" with "applications"',
            '/Apps/' => 'Please replace "%s" with "Applications"',
            '/typehint/' => 'Please replace "%s" with "type-hint"',
            '/Typehint/' => 'Please replace "%s" with "Type-hint"',
            '/encoding="utf-8"/' => 'Please replace "%s" with "encoding="UTF-8""',
            '/\$fileSystem/' => 'Please replace "%s" with "$filesystem"',
            '/Content-type/' => 'Please replace "%s" with "Content-Type"',
            '/\-\-env prod/' => 'Please replace "%s" with "--env=prod"',
            '/\-\-env test/' => 'Please replace "%s" with "--env=test"',
            '/End 2 End/i' => 'Please replace "%s" with "End-to-End"',
            '/E2E/' => 'Please replace "%s" with "End-to-End"',
            '/informations/' => 'Please replace "%s" with "information"',
            '/Informations/' => 'Please replace "%s" with "Information"',
            '/performances/' => 'Please replace "%s" with "performance"',
            '/Performances/' => 'Please replace "%s" with "Performance"',
            "/``'%kernel.debug%'``/" => 'Please replace "%s" with "``%%kernel.debug%%``"',
            '/PHPdoc/' => 'Please replace "%s" with "PHPDoc"',
            '/eg\./' => 'Please replace "%s" with "e.g."',
        ];
    }
}
