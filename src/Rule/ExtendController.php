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

#[Description('Ensure `Controller` and the corresponding namespace `Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller` is used. Instead of `Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController`.')]
class ExtendController extends AbstractRule implements LineContentRule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number, string $filename): ViolationInterface
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->match('/^class(.*)extends AbstractController$/')) {
            return Violation::from(
                'Please extend Controller instead of AbstractController',
                $filename,
                $number + 1,
                $line,
            );
        }

        if ($line->clean()->match('/^use Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController;/')) {
            return Violation::from(
                'Please use "Symfony\Bundle\FrameworkBundle\Controller\Controller" instead of "Symfony\Bundle\FrameworkBundle\Controller\AbstractController"',
                $filename,
                $number + 1,
                $line,
            );
        }

        return NullViolation::create();
    }
}
