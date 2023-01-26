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
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;

final class BlankLineAfterFilepathInCodeBlockTest extends \App\Tests\UnitTestCase
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
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        static::assertEquals(
            $expected,
            (new BlankLineAfterFilepathInCodeBlock())->check($sample->lines(), $sample->lineNumber(), 'filename')
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkProvider(): \Generator
    {
        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkPhpProvider(): \Generator
    {
        yield [
            Violation::from(
                'Please add a blank line after "// src/Handler/Collection.php"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: php',
                '',
                '    // src/Handler/Collection.php',
                '    namespace App\\Handler;',
            ]),
        ];
        yield [
            NullViolation::create(),
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
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkYmlProvider(): \Generator
    {
        yield [
            Violation::from(
                'Please add a blank line after "# config/services.yml"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: yml',
                '',
                '    # config/services.yml',
                '    services:',
            ]),
        ];
        yield [
            NullViolation::create(),
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
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkYamlProvider(): \Generator
    {
        yield [
            Violation::from(
                'Please add a blank line after "# config/services.yaml"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: yaml',
                '',
                '    # config/services.yaml',
                '    services:',
            ]),
        ];
        yield [
            NullViolation::create(),
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
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkXmlProvider(): \Generator
    {
        yield [
            Violation::from(
                'Please add a blank line after "<!-- config/services.xml -->"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!-- config/services.xml -->',
                '    <foo\/>',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!-- config/services.xml -->',
                '',
                '    <foo\/>',
            ]),
        ];
        yield [
            Violation::from(
                'Please add a blank line after "<!--config/services.xml-->"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: xml',
                '',
                '    <!--config/services.xml-->',
                '    <foo\/>',
            ]),
        ];
        yield [
            NullViolation::create(),
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
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public function checkTwigProvider(): \Generator
    {
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: twig',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: jinja',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
            new RstSample([
                '.. code-block:: jinja',
                '',
                '    {# templates/index.html.twig #}',
                '',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                1,
                ''
            ),
            new RstSample([
                '.. code-block:: html+jinja',
                '',
                '    {# templates/index.html.twig #}',
                '    {% set foo = "bar" %}',
            ]),
        ];
        yield [
            NullViolation::create(),
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
