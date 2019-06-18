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
use PHPUnit\Framework\TestCase;

class EnsureOrderOfCodeBlocksInConfigurationBlockTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new EnsureOrderOfCodeBlocksInConfigurationBlock())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function validProvider()
    {
        $valid = <<<RST
.. configuration-block::

    .. code-block:: php-annotations

        test

    .. code-block:: yaml

        test

    .. code-block:: xml

        test
        
    .. code-block:: php

        test
RST;

        $valid2 = <<<RST
.. configuration-block::

    .. code-block:: html

        test

    .. code-block:: php-annotations

        test

    .. code-block:: yaml

        test

    .. code-block:: xml

        test
        
    .. code-block:: php

        test
RST;

        $invalid_but_valid_because_of_xliff = <<<RST
.. configuration-block::

    .. code-block:: xml

        <xliff version="1.2">test</xliff>

    .. code-block:: php-annotations

        test

    .. code-block:: yaml

        test

    .. code-block:: php

        test
RST;

        return [
            [
                null,
                new RstSample($valid),
            ],
            [
                null,
                new RstSample($valid2),
            ],
            [
                null,
                new RstSample($invalid_but_valid_because_of_xliff),
            ],
        ];
    }

    public function invalidProvider()
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
        
    .. code-block:: php

        test
        
    .. code-block:: php-annotations

        test         
RST;

        $valid_but_invalid_in_the_end_because_of_xliff_content = <<<RST
.. configuration-block::

    .. code-block:: php-annotations

        test

    .. code-block:: yaml

        test

    .. code-block:: xml

        <xliff version="1.2">test</xliff>

    .. code-block:: php

        test
RST;

        return [
            [
                'Please use the following order for your code blocks: "php-annotations, yaml, xml, php"',
                new RstSample($invalid),
            ],
            [
                'Please use the following order for your code blocks: "php-annotations, yaml, xml, php"',
                new RstSample($invalid2),
            ],
            'valid but invalid in the end because of xliff content' => [
                'Please use the following order for your code blocks: "xml, php-annotations, yaml, php"',
                new RstSample($valid_but_invalid_in_the_end_because_of_xliff_content),
            ],
        ];
    }
}
