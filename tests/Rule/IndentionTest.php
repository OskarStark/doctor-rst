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

namespace App\Tests\Rule;

use App\Rule\Indention;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class IndentionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, int $size, RstSample $sample)
    {
        $rule = (new Indention());
        $rule->setOptions(['size' => $size]);

        $this->assertSame($expected, $rule->check($sample->getContent(), $sample->getLineNumber()));
    }

    public function checkProvider(): \Generator
    {
        yield [null, 4, new RstSample('')];
        yield [
            null,
            4,
            new RstSample(<<<CONTENT
Headline

    Content
CONTENT
            , 2),
        ];

        yield [
            null,
            4,
            new RstSample(<<<CONTENT
Headline
Content
CONTENT
            , 1),
        ];

        yield [
            null,
            4,
            new RstSample(<<<CONTENT
Headline

CONTENT
            , 1),
        ];

        yield 'wrong without blank line' => [
            'Please add 4 spaces for every indention.',
            4,
            new RstSample(<<<CONTENT
Headline
========
  Content
CONTENT
            , 2),
        ];

        yield 'wrong with blank line' => [
            'Please add 4 spaces for every indention.',
            4,
            new RstSample(<<<CONTENT
Headline
========

  Content
CONTENT
            , 3),
        ];

        yield [
            null,
            4,
            new RstSample(<<<CONTENT
.. index::
   single: Cache

HTTP Cache
==========

The nature of rich web applications means that they're dynamic. No matter   
CONTENT
            , 1),
        ];

        $php_comment_example = <<<'CONTENT'
Code here::

    class MicroController extends Controller
    {
        /**
         * @Route("/random/{limit}")
         */
        public function randomAction($limit)
        {
CONTENT;

        yield 'first line of the php comment' => [
            null,
            4,
            new RstSample($php_comment_example, 4),
        ];

        yield 'middle of the php comment' => [
            null,
            4,
            new RstSample($php_comment_example, 5),
        ];

        yield 'last line of the php comment' => [
            null,
            4,
            new RstSample($php_comment_example, 6),
        ];

        yield 'list item (#) first line' => [
            null,
            4,
            new RstSample(<<<'CONTENT'
#. At the beginning of the request, the Firewall checks the firewall map
   to see if any firewall should be active for this URL;
CONTENT
            ),
        ];

        yield 'list item (#) second line' => [
            null,
            4,
            new RstSample(<<<'CONTENT'
#. At the beginning of the request, the Firewall checks the firewall map
   to see if any firewall should be active for this URL;
CONTENT
            , 1),
        ];

        yield 'list item (*) first line' => [
            null,
            4,
            new RstSample(<<<'CONTENT'
* At the beginning of the request, the Firewall checks the firewall map
  to see if any firewall should be active for this URL;
CONTENT
            ),
        ];

        yield 'list item (*) second line' => [
            null,
            4,
            new RstSample(<<<'CONTENT'
* At the beginning of the request, the Firewall checks the firewall map
  to see if any firewall should be active for this URL;
CONTENT
            , 1),
        ];

        yield 'special char "├─"' => [
            null,
            4,
            new RstSample(<<<'CONTENT'
  ├─ app.php
CONTENT
            ),
        ];

        yield 'special char "└─"' => [
            null,
            4,
            new RstSample(<<<'CONTENT'
  └─ ...
CONTENT
            ),
        ];
    }
}
