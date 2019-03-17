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
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new EnsureOrderOfCodeBlocksInConfigurationBlock())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function validProvider()
    {
        $valid = <<<CONTENT
.. configuration-block::

    .. code-block:: php-annotations

        test

    .. code-block:: yaml

        test

    .. code-block:: xml

        test
        
    .. code-block:: php

        test
CONTENT;

        $valid2 = <<<CONTENT
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
CONTENT;

        return [
            [
                null,
                new RstSample($valid),
            ],
            [
                null,
                new RstSample($valid2),
            ],
        ];
    }

    public function invalidProvider()
    {
        $invalid = <<<CONTENT
.. configuration-block::

    .. code-block:: yaml

        test

    .. code-block:: xml

        test
        
    .. code-block:: php

        test
        
    .. code-block:: php-annotations

        test         
CONTENT;

        $invalid2 = <<<CONTENT
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
CONTENT;

        return [
            [
                'Please use the following order for your code blocks: "php-annotations, yaml, xml, php"',
                new RstSample($invalid),
            ],
            [
                'Please use the following order for your code blocks: "php-annotations, yaml, xml, php"',
                new RstSample($invalid2),
            ],
        ];
    }
}
