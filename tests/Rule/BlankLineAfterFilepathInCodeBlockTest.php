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

use App\Rule\BlankLineAfterFilepathInCodeBlock;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BlankLineAfterFilepathInCodeBlockTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkPhpProvider')]
    #[DataProvider('checkProvider')]
    #[DataProvider('checkTwigProvider')]
    #[DataProvider('checkXmlProvider')]
    #[DataProvider('checkYamlProvider')]
    #[DataProvider('checkYmlProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new BlankLineAfterFilepathInCodeBlock())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('temp'),
        ];
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function checkPhpProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a blank line after "// src/Handler/Collection.php"',
                'filename',
                3,
                '// src/Handler/Collection.php',
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
    public static function checkYmlProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a blank line after "# config/services.yml"',
                'filename',
                3,
                '# config/services.yml',
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
    public static function checkYamlProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a blank line after "# config/services.yaml"',
                'filename',
                3,
                '# config/services.yaml',
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
    public static function checkXmlProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a blank line after "<!-- config/services.xml -->"',
                'filename',
                3,
                '<!-- config/services.xml -->',
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
                3,
                '<!--config/services.xml-->',
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
    public static function checkTwigProvider(): iterable
    {
        yield [
            Violation::from(
                'Please add a blank line after "{# templates/index.html.twig #}"',
                'filename',
                3,
                '{# templates/index.html.twig #}',
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
                3,
                '{# templates/index.html.twig #}',
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
                3,
                '{# templates/index.html.twig #}',
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
