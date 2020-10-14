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
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("Ensure `AbstractController` and the corresponding namespace `Symfony\Bundle\FrameworkBundle\Controller\AbstractController` is used. Instead of `Symfony\Bundle\FrameworkBundle\Controller\Controller`.")
 */
class ExtendAbstractController extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RuleGroup::Symfony()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current()->cleanU();

        if ($line->match('/^class(.*)extends Controller$/')) {
            return 'Please extend AbstractController instead of Controller';
        }

        if ($line->match('/^use Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\Controller;/')) {
            return 'Please use "Symfony\Bundle\FrameworkBundle\Controller\AbstractController" instead of "Symfony\Bundle\FrameworkBundle\Controller\Controller"';
        }

        return null;
    }
}
