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

use App\Rule\EnsureOrderOfCodeBlocksInConfigurationBlock;
use App\Tests\RstSample;

final class EnsureOrderOfCodeBlocksInConfigurationBlockTest extends \App\Tests\UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(?string $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            (new EnsureOrderOfCodeBlocksInConfigurationBlock())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: null, 1: RstSample}>
     */
    public function validProvider(): \Generator
    {
        $valid = <<<RST
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

        $valid2 = <<<RST
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

        $valid3 = <<<RST
.. configuration-block::

    .. code-block:: php-symfony

        test
        
    .. code-block:: php-standalone

        test
RST;

        $invalid_but_valid_because_of_xliff = <<<RST
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

        $valid_too_with_xliff = <<<RST
.. configuration-block::

    .. code-block:: yaml

        test

    .. code-block:: xml

        <xliff version="1.2">test</xliff>

    .. code-block:: php

        test
RST;

        $valid_all_the_same = <<<RST
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

        $translationDebug = <<<RST
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
            null,
            new RstSample($valid),
        ];
        yield 'valid 2' => [
            null,
            new RstSample($valid2),
        ];
        yield 'valid 3' => [
            null,
            new RstSample($valid3),
        ];
        yield 'first invalid, but valid because of xliff' => [
            null,
            new RstSample($invalid_but_valid_because_of_xliff),
        ];
        yield 'valid too with xliff' => [
            null,
            new RstSample($valid_too_with_xliff),
        ];
        yield 'valid all the same' => [
            null,
            new RstSample($valid_all_the_same),
        ];
        yield 'translation debug' => [
            null,
            new RstSample($translationDebug),
        ];
    }

    /**
     * @return \Generator<array{0: string, 1: RstSample}>
     */
    public function invalidProvider(): \Generator
    {
        $invalid = <<<RST
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

        $invalid2 = <<<RST
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

        $invalid3 = <<<RST
.. configuration-block::

    .. code-block:: php-standalone

        test

    .. code-block:: php-symfony

        test
RST;

        yield [
            'Please use the following order for your code blocks: "php-annotations, yaml, xml, php"',
            new RstSample($invalid),
        ];
        yield [
            'Please use the following order for your code blocks: "php-annotations, php-attributes, yaml, xml, php"',
            new RstSample($invalid2),
        ];
        yield [
            'Please use the following order for your code blocks: "php-symfony, php-standalone"',
            new RstSample($invalid3),
        ];
    }
}
