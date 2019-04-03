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

use App\Rule\OrderedUseStatements;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class OrderedUseStatementsTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new OrderedUseStatements())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider(): \Generator
    {
        $codeBlocks = [
            '.. code-block:: php',
            '.. code-block:: php-annotations',
            'A php code block follows::',
        ];

        // VALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                null,
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                null,
                new RstSample([
                    $codeBlock,
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                ]),
            ];
        }

        // INVALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                'Please reorder the use statements alphabetical',
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                'Please reorder the use statements alphabetical',
                new RstSample([
                    $codeBlock,
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];
        }

        $valid_with_trait = <<<'CONTENT'
In addition, the Console component provides a PHP trait called ``LockableTrait``
that adds two convenient methods to lock and release commands::

    // ...
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Command\LockableTrait;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    class UpdateContentsCommand extends Command
    {
        use LockableTrait;
CONTENT;

        $valid_with_two_code_examples_in_one_block = <<<'CONTENT'
    .. code-block:: php

        // src/AppBundle/Entity/Address.php
        namespace AppBundle\Entity;

        use Symfony\Component\Validator\Constraints as Assert;
        use Symfony\Component\Validator\Mapping\ClassMetadata;

        class Address
        {
            protected $street;
            protected $zipCode;

            public static function loadValidatorMetadata(ClassMetadata $metadata)
            {
                $metadata->addPropertyConstraint('street', new Assert\NotBlank());
                $metadata->addPropertyConstraint('zipCode', new Assert\NotBlank());
                $metadata->addPropertyConstraint('zipCode', new Assert\Length(["max" => 5]));
            }
        }

        // src/AppBundle/Entity/Author.php
        namespace AppBundle\Entity;

        use Symfony\Component\Validator\Constraints as Assert;
        use Symfony\Component\Validator\Mapping\ClassMetadata;

        class Author
        {
            protected $firstName;
            protected $lastName;
            protected $address;

            public static function loadValidatorMetadata(ClassMetadata $metadata)
            {
                $metadata->addPropertyConstraint('firstName', new Assert\NotBlank());
                $metadata->addPropertyConstraint('firstName', new Assert\Length(["min" => 4]));
                $metadata->addPropertyConstraint('lastName', new Assert\NotBlank());
            }
        }
CONTENT;


        yield [null, new RstSample($valid_with_trait, 1)];
        yield [null, new RstSample($valid_with_two_code_examples_in_one_block)];
    }
}
