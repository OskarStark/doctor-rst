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
use App\Traits\DirectiveTrait;
use App\Value\Lines;
use App\Value\RuleGroup;

/**
 * @Description("A namespace declaration in a PHP code-block should only contain backslashes.")
 * @InvalidExample("namespace Foo/Bar;")
 * @ValidExample("namespace Foo\Bar;")
 */
class OnlyBackslashesInNamespaceInPhpCodeBlock extends AbstractRule implements Rule
{
    use DirectiveTrait;

    public static function getGroups(): array
    {
        return [
            RuleGroup::Sonata(),
            RuleGroup::Symfony(),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if ($line->clean()->lower()->startsWith('namespace')
            && $line->clean()->containsAny('/')
            && $this->inPhpCodeBlock($lines, $number)
        ) {
            return sprintf(
                'Please check "%s", it should not contain "/"',
                $line->clean()->toString()
            );
        }

        return null;
    }
}
