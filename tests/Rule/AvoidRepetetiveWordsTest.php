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

namespace App\Tests\Rule;

use App\Rule\AvoidRepetetiveWords;
use App\Tests\RstSample;
use App\Value\NullViolation;
use App\Value\Violation;

final class AvoidRepetetiveWordsTest extends AbstractLineContentRuleTestCase
{
    public function createRule(): AvoidRepetetiveWords
    {
        return new AvoidRepetetiveWords();
    }

    public static function checkProvider(): iterable
    {
        $whitelist = [
            '...',
        ];

        foreach ($whitelist as $word) {
            yield \sprintf('valid whitelist: %s', $word) => [NullViolation::create(), new RstSample(\sprintf('%s %s %s', $word, $word, $word))];
        }

        $valid = '';
        $invalid = 'the cached items will not not be invalidated unless you clear OPcache.';

        yield 'empty' => [NullViolation::create(), new RstSample('')];
        yield 'blank line' => [NullViolation::create(), new RstSample('\n')];
        yield 'directive' => [NullViolation::create(), new RstSample('.. code-block:: php')];
        yield 'link' => [NullViolation::create(), new RstSample('.. _`Symfony`: https://symfony.com')];
        yield [NullViolation::create(), new RstSample($valid)];

        yield [
            Violation::from(
                'The word "not" is used more times in a row.',
                'filename',
                1,
                $invalid,
            ),
            new RstSample($invalid),
        ];

        yield [NullViolation::create(), new RstSample([
            '.. code-block:: php',
            '',
            '    public function foo($bar, $bar)',
        ], 2)];
        yield 'php comment' => [
            Violation::from(
                'The word "is" is used more times in a row.',
                'filename',
                3,
                '// this is is a comment',
            ),
            new RstSample([
                '.. code-block:: php',
                '',
                '    // this is is a comment',
                '    public function foo($bar, $bar)',
            ], 2),
        ];

        yield [NullViolation::create(), new RstSample([
            '.. code-block:: xml',
            '',
            '    <xml value="1" value="1">',
        ], 2)];
        yield 'xml comment' => [
            Violation::from(
                'The word "is" is used more times in a row.',
                'filename',
                3,
                '<!-- this is is a comment -->',
            ),
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!-- this is is a comment -->',
                '    <xml value="1" value="1">',
            ], 2),
        ];

        yield [NullViolation::create(), new RstSample([
            '.. code-block:: twig',
            '',
            '    {{ value }}',
        ], 2)];
        yield 'twig comment' => [
            Violation::from(
                'The word "is" is used more times in a row.',
                'filename',
                3,
                '{# this is is a comment #}',
            ),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# this is is a comment #}',
                '    {{ value }}',
            ], 2),
        ];

        yield [NullViolation::create(), new RstSample([
            '.. code-block:: yaml',
            '',
            '    services: ~',
        ], 2)];
        yield 'yaml comment' => [
            Violation::from(
                'The word "is" is used more times in a row.',
                'filename',
                3,
                '# this is is a comment',
            ),
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # this is is a comment',
                '    services   : ~',
            ], 2),
        ];

        yield 'numeric repetition' => [
            NullViolation::create(),
            new RstSample('This is valid 123 123'),
        ];

        yield 'numeric repetition with comma' => [
            NullViolation::create(),
            new RstSample('224, 165, 141, 224, 164, 164, 224, 165, 135])'),
        ];
    }
}
