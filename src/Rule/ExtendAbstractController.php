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
use App\Handler\Registry;
use App\Rst\RstParser;

/**
 * @Description("Ensure `AbstractController` and the corresponding namespace `Symfony\Bundle\FrameworkBundle\Controller\AbstractController` is used. Instead of `Symfony\Bundle\FrameworkBundle\Controller\Controller`.")
 */
class ExtendAbstractController extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [Registry::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        $line = RstParser::clean($line);

        if (preg_match('/^class(.*)extends Controller$/', $line)) {
            return 'Please extend AbstractController instead of Controller';
        }

        if (strstr($line, 'use Symfony\Bundle\FrameworkBundle\Controller\Controller;')) {
            return 'Please use "Symfony\Bundle\FrameworkBundle\Controller\AbstractController" instead of "Symfony\Bundle\FrameworkBundle\Controller\Controller"';
        }
    }
}
