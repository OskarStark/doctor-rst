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
use App\Handler\RulesHandler;
use App\Rst\RstParser;

/**
 * @Description("Ensure `Controller` and the corresponding namespace `Symfony\Bundle\FrameworkBundle\Controller\Controller` is used. Instead of `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`.")
 */
class ExtendController extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        $line = RstParser::clean($line);

        if (preg_match('/^class(.*)extends AbstractController$/', $line)) {
            return 'Please extend Controller instead of AbstractController';
        }

        if (strstr($line, 'use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;')) {
            return 'Please use "Symfony\Bundle\FrameworkBundle\Controller\Controller" instead of "Symfony\Bundle\FrameworkBundle\Controller\AbstractController"';
        }
    }
}
