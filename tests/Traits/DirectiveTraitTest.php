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

namespace App\Tests\Traits;

use App\Rst\RstParser;
use App\Tests\RstSample;
use App\Traits\DirectiveTrait;
use PHPUnit\Framework\TestCase;

class DirectiveTraitTest extends TestCase
{
    private $traitWrapper;

    protected function setUp()
    {
        $this->traitWrapper = new class() {
            use DirectiveTrait {
                DirectiveTrait::in as public;
            }
        };
    }

    /**
     * @test
     */
    public function methodExists()
    {
        $this->assertTrue(method_exists($this->traitWrapper, 'in'));
    }

    /**
     * @test
     *
     * @dataProvider inProvider
     */
    public function in(bool $expected, RstSample $sample, string $directive, ?array $types = null)
    {
        $this->assertSame(
            $expected,
            $this->traitWrapper->in($directive, $sample->getContent(), $sample->getLineNumber(), $types)
        );
    }

    public function inProvider()
    {
        $no_code_block = <<<'CONTENT'
I am just a cool text!
CONTENT;

        yield [
            false,
            new RstSample($no_code_block),
            RstParser::DIRECTIVE_CODE_BLOCK,
        ];

        $in_code_block = <<<'CONTENT'
.. code-block:: php

    // I am just a cool text!
CONTENT;

        yield [
            true,
            new RstSample($in_code_block, 2),
            RstParser::DIRECTIVE_CODE_BLOCK,
        ];

        yield [
            true,
            new RstSample($in_code_block, 2),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_PHP],
        ];

        $in_code_block_but_wrong_type = <<<'CONTENT'
.. code-block:: php

    // I am just a cool text!
CONTENT;

        yield [
            false,
            new RstSample($in_code_block, 2),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_JAVASCRIPT],
        ];
    }
}
