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

use App\Rule\EnsureOrderOfCodeBlocksInConfigurationBlock;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnsureOrderOfCodeBlocksInConfigurationBlockTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new EnsureOrderOfCodeBlocksInConfigurationBlock())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function validProvider(): iterable
    {
        $valid = <<<'RST'
.. configuration-block::

    .. code-block:: php-symfony

        test

    .. code-block:: php-annotations

        test

    .. code-block:: php-attributes

        test

    .. code-block:: yaml

        test

    .. code-block:: xml

        test

    .. code-block:: php

        test

    .. code-block:: php-standalone

        test
RST;

        $valid2 = <<<'RST'
.. configuration-block::

    .. code-block:: html

        test

    .. code-block:: php-symfony

        test

    .. code-block:: php-annotations

        test

    .. code-block:: php-attributes

        test

    .. code-block:: yaml

        test

    .. code-block:: xml

        test

    .. code-block:: php

        test

    .. code-block:: php-standalone

        test
RST;

        $valid3 = <<<'RST'
.. configuration-block::

    .. code-block:: php-symfony

        test

    .. code-block:: php-standalone

        test
RST;

        $invalid_but_valid_because_of_xliff = <<<'RST'
.. configuration-block::

    .. code-block:: xml

        <xliff version="1.2">test</xliff>

    .. code-block:: php-annotations

        test

    .. code-block:: php-attributes

        test

    .. code-block:: yaml

        test

    .. code-block:: php

        test
RST;

        $valid_too_with_xliff = <<<'RST'
.. configuration-block::

    .. code-block:: yaml

        test

    .. code-block:: xml

        <xliff version="1.2">test</xliff>

    .. code-block:: php

        test
RST;

        $valid_all_the_same = <<<'RST'
.. configuration-block::

    .. code-block:: yaml

        # config/packages/fos_rest.yaml

        fos_rest:
            param_fetcher_listener: true
            body_listener:          true
            format_listener:        true
            view:
                view_response_listener: force
            body_converter:
                enabled: true
                validate: true

    .. code-block:: yaml

        # config/packages/sensio_framework_extra.yaml

        sensio_framework_extra:
            view:    { annotations: false }
            router:  { annotations: true }
            request: { converters: true }

    .. code-block:: yaml

        # config/packages/twig.yaml

        twig:
            exception_controller: 'FOS\RestBundle\Controller\ExceptionController::showAction'
RST;

        $translationDebug = <<<'RST'
.. configuration-block::

    .. code-block:: xml

        <!-- translations/messages.fr.xlf -->
        <?xml version="1.0"?>
        <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
            <file source-language="en" datatype="plaintext" original="file.ext">
                <body>
                    <trans-unit id="1">
                        <source>Symfony is great</source>
                        <target>J'aime Symfony</target>
                    </trans-unit>
                </body>
            </file>
        </xliff>

    .. code-block:: yaml

        # translations/messages.fr.yaml
        Symfony is great: J'aime Symfony

    .. code-block:: php

        // translations/messages.fr.php
        return [
            'Symfony is great' => 'J\'aime Symfony',
        ];
RST;

        yield 'valid 1' => [
            NullViolation::create(),
            new RstSample($valid),
        ];
        yield 'valid 2' => [
            NullViolation::create(),
            new RstSample($valid2),
        ];
        yield 'valid 3' => [
            NullViolation::create(),
            new RstSample($valid3),
        ];
        yield 'first invalid, but valid because of xliff' => [
            NullViolation::create(),
            new RstSample($invalid_but_valid_because_of_xliff),
        ];
        yield 'valid too with xliff' => [
            NullViolation::create(),
            new RstSample($valid_too_with_xliff),
        ];
        yield 'valid all the same' => [
            NullViolation::create(),
            new RstSample($valid_all_the_same),
        ];
        yield 'translation debug' => [
            NullViolation::create(),
            new RstSample($translationDebug),
        ];
    }

    /**
     * @return \Generator<array{0: ViolationInterface, 1: RstSample}>
     */
    public static function invalidProvider(): iterable
    {
        $invalid = <<<'RST'
.. configuration-block::

    .. code-block:: yaml

        test

    .. code-block:: xml

        test

    .. code-block:: php

        test

    .. code-block:: php-annotations

        test
RST;

        $invalid2 = <<<'RST'
.. configuration-block::

    .. code-block:: html

        test

    .. code-block:: yaml

        test

    .. code-block:: xml

        test

    .. code-block:: php-attributes

        test

    .. code-block:: php

        test

    .. code-block:: php-annotations

        test
RST;

        $invalid3 = <<<'RST'
.. configuration-block::

    .. code-block:: php-standalone

        test

    .. code-block:: php-symfony

        test
RST;

        yield [
            Violation::from(
                'Please use the following order for your code blocks: "php-annotations, yaml, xml, php"',
                'filename',
                1,
                '.. configuration-block::',
            ),
            new RstSample($invalid),
        ];
        yield [
            Violation::from(
                'Please use the following order for your code blocks: "php-annotations, php-attributes, yaml, xml, php"',
                'filename',
                1,
                '.. configuration-block::',
            ),
            new RstSample($invalid2),
        ];
        yield [
            Violation::from(
                'Please use the following order for your code blocks: "php-symfony, php-standalone"',
                'filename',
                1,
                '.. configuration-block::',
            ),
            new RstSample($invalid3),
        ];
    }
}
