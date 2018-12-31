<?php

declare(strict_types=1);

/*
 * This file is part of the rst-checker.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Rule;

use App\Util\Util;

class CodeBlockPhpDirectiveHasOpenTag implements Rule
{
    public function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!Util::codeBlockDirectiveIsTypeOf($line, Util::CODE_BLOCK_PHP)) {
            return;
        }

        $lines->next();
        $lines->next();

        // check if next line is "<?php"
        $nextLine = $lines->current();

        if (Util::clean($nextLine) !== '<?php') {
            return sprintf('Please add PHP open tag after "%s" directive', $line);
        }
    }
}
