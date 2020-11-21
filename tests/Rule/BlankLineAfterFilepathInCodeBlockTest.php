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

use App\Rule\BlankLineAfterFilepathInCodeBlock;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

final class BlankLineAfterFilepathInCodeBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     * @dataProvider checkPhpProvider
     * @dataProvider checkYmlProvider
     * @dataProvider checkYamlProvider
     * @dataProvider checkXmlProvider
     * @dataProvider checkTwigProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        static::assertSame(
            $expected,
            (new BlankLineAfterFilepathInCodeBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: null, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            null,
            new RstSample('temp'),
        ];
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkPhpProvider(): \Generator
    {
        yield [
            'Please add a blank line after "// src/Handler/Collection.php"',
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Handler/Collection.php',
                '    namespace App\\Handler;',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Handler/Collection.php',
                '',
                '    namespace App\\Handler;',
            ]),
        ];
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkYmlProvider(): \Generator
    {
        yield [
            'Please add a blank line after "# config/services.yml"',
            new RstSample([
                '.. code-block:: yml',
                '',
                '    # config/services.yml',
                '    services:',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: yml',
                '',
                '    # config/services.yml',
                '',
                '    services:',
            ]),
        ];
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkYamlProvider(): \Generator
    {
        yield [
            'Please add a blank line after "# config/services.yaml"',
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # config/services.yaml',
                '    services:',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # config/services.yaml',
                '',
                '    services:',
            ]),
        ];
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkXmlProvider(): \Generator
    {
        yield [
            'Please add a blank line after "<!-- config/services.xml -->"',
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!-- config/services.xml -->',
                '    <foo\/>',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!-- config/services.xml -->',
                '',
                '    <foo\/>',
            ]),
        ];
        yield [
            'Please add a blank line after "<!--config/services.xml-->"',
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!--config/services.xml-->',
                '    <foo\/>',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!--config/services.xml-->',
                '',
                '    <foo\/>',
            ]),
        ];
    }

    /**
     * @return \Generator<array{0: string|null, 1: RstSample}>
     */
    public function checkTwigProvider(): \Generator
    {
        yield [
            'Please add a blank line after "{# templates/index.html.twig #}"',
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            'Please add a blank line after "{# templates/index.html.twig #}"',
            new RstSample([
                '.. code-block:: jinja',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: jinja',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            'Please add a blank line after "{# templates/index.html.twig #}"',
            new RstSample([
                '.. code-block:: html+jinja',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '.. code-block:: html+jinja',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
    }
}
