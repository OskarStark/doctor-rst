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

use App\Handler\RulesHandler;

class YamlInsteadOfYmlSuffix extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match('/\.travis\.yml/', trim($line))) {
            return;
        }

        if (preg_match('/^.. code-block:: yml$/', trim($line))) {
            return 'Please use ".. code-block:: yaml" instead of ".. code-block:: yml"';
        }

        if (preg_match('/\.yml/', $line)) {
            return 'Please use ".yaml" instead of ".yml"';
        }
    }
}
