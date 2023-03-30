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
use App\Value\Lines;
use App\Value\NullViolation;
use App\Value\RuleGroup;
use App\Value\Violation;
use App\Value\ViolationInterface;

#[Description('Ensure `AbstractController` and the corresponding namespace `Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController` is used. Instead of `Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller`.')]
class ExtendAbstractController extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/^class(.*)extends Controller$/')) {
            return Violation::from(
                'Please extend AbstractController instead of Controller',
                $filename,
                $number + 1,
                $line,
            );
        }

        if ($line->clean()->match('/^use Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\Controller;/')) {
            return Violation::from(
                'Please use "Symfony\Bundle\FrameworkBundle\Controller\AbstractController" instead of "Symfony\Bundle\FrameworkBundle\Controller\Controller"',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
