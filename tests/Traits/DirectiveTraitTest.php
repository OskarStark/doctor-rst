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
use App\Tests\Util\DirectiveTraitWrapper;

final class DirectiveTraitTest extends \App\Tests\UnitTestCase
{
    private DirectiveTraitWrapper $traitWrapper;

    protected function setUp(): void
    {
        $this->traitWrapper = new DirectiveTraitWrapper();
    }

    /**
     * @test
     */
    public function methodExists(): void
    {
        static::assertTrue(method_exists($this->traitWrapper, 'in'));
    }

    /**
     * @test
     *
     * @dataProvider inPhpCodeBlockProvider
     */
    public function inPhpCodeBlock(bool $expected, RstSample $sample): void
    {
        static::assertSame(
            $expected,
            $this->traitWrapper->inPhpCodeBlock(clone $sample->lines(), $sample->lineNumber())
        );
    }

    public function inPhpCodeBlockProvider(): \Generator
    {
        yield [
            true,
            new RstSample([
                '.. code-block:: php',
                '',
                '    /*',
                '     * {@inheritdoc}',
                '     */',
            ], 2),
        ];

        yield [
            false,
            new RstSample([
                '.. code-block:: xml',
                '',
                '    /*',
                '     * {@inheritdoc}',
                '     */',
            ], 2),
        ];
    }

    /**
     * @test
     *
     * @dataProvider inProvider
     */
    public function in(bool $expected, RstSample $sample, string $directive, ?array $types = null): void
    {
        static::assertSame(
            $expected,
            $this->traitWrapper->in($directive, clone $sample->lines(), $sample->lineNumber(), $types)
        );
    }

    public function inProvider(): \Generator
    {
        $no_code_block = <<<'RST'
I am just a cool text!
RST;

        yield [
            false,
            new RstSample($no_code_block),
            RstParser::DIRECTIVE_CODE_BLOCK,
        ];

        $in_code_block = <<<'RST'
.. code-block:: php

    // I am just a cool text!
RST;

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

        yield [
            false,
            new RstSample($in_code_block, 2),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_JAVASCRIPT],
        ];

        yield [
            false,
            new RstSample(<<<RST

.. _env-var-processors:

Environment Variable Processors
===============================
RST
                , 1),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_YAML],
        ];

        $invalid = <<<'RST'
Coding Standards
================

Symfony code is contributed by thousands of developers around the world. To make
every piece of code look and feel familiar, Symfony defines some coding standards
that all contributions must follow.

These Symfony coding standards are based on the `PSR-1`_, `PSR-2`_ and `PSR-4`_
standards, so you may already know most of them.

Making your Code Follow the Coding Standards
--------------------------------------------

Instead of reviewing your code manually, Symfony makes it simple to ensure that
your contributed code matches the expected code syntax. First, install the
`PHP CS Fixer tool`_ and then, run this command to fix any problem:

.. code-block:: terminal

    $ cd your-project/
    $ php php-cs-fixer.phar fix -v

If you forget to run this command and make a pull request with any syntax issue,
our automated tools will warn you about that and will provide the solution.

Symfony Coding Standards in Detail
----------------------------------

If you want to learn about the Symfony coding standards in detail, here's a
short example containing most features described below::

    /*
     * This file is part of the Symfony package.
     *
     * (c) Fabien Potencier <fabien@symfony.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace Acme;

    /**
     * Coding standards demonstration.
     */
    class FooBar
    {
        const SOME_CONST = 42;

        /**
         * @var string
         */
        private $fooBar;

        /**
         * @param string $dummy Some argument description
         */
        public function __construct($dummy)
        {
            $this->fooBar = $this->transformText($dummy);
        }

        /**
         * @return string
         *
         * @deprecated
         */
        public function someDeprecatedMethod()
        {
            @trigger_error(sprintf('The %s() method is deprecated since version 2.8 and will be removed in 3.0. Use Acme\Baz::someMethod() instead.', __METHOD__), E_USER_DEPRECATED);

            return Baz::someMethod();
        }

        /**
         * Transforms the input given as first argument.
         *
         * @param bool|string $dummy   Some argument description
         * @param array       $options An options collection to be used within the transformation
         *
         * @return string|null The transformed input
         *
         * @throws \RuntimeException When an invalid option is provided
         */
        private function transformText($dummy, array $options = [])
        {
            $defaultOptions = [
                'some_default' => 'values',
                'another_default' => 'more values',
            ];

            foreach ($options as $option) {
                if (!in_array($option, $defaultOptions)) {
                    throw new \RuntimeException(sprintf('Unrecognized option "%s"', $option));
                }
            }

            $mergedOptions = array_merge(
                $defaultOptions,
                $options
            );

            if (true === $dummy) {
                return null;
            }

            if ('string' === $dummy) {
                if ('values' === $mergedOptions['some_default']) {
                    return substr($dummy, 0, 5);
                }

                return ucwords($dummy);
            }
        }

        /**
         * Performs some basic check for a given value.
         *
         * @param mixed $value     Some value to check against
         * @param bool  $theSwitch Some switch to control the method's flow
         *
         * @return bool|void The resultant check if $theSwitch isn't false, void otherwise
         */
        private function reverseBoolean($value = null, $theSwitch = false)
        {
            if (!$theSwitch) {
                return;
            }

            return !$value;
        }
    }

Structure
~~~~~~~~~

* Add a single space after each comma delimiter;

* Add a single space around binary operators (``==``, ``&&``, ...), with
  the exception of the concatenation (``.``) operator;

* Place unary operators (``!``, ``--``, ...) adjacent to the affected variable;

* Always use `identical comparison`_ unless you need type juggling;

* Use `Yoda conditions`_ when checking a variable against an expression to avoid
  an accidental assignment inside the condition statement (this applies to ``==``,
  ``!=``, ``===``, and ``!==``);

* Add a comma after each array item in a multi-line array, even after the
  last one;

* Add a blank line before ``return`` statements, unless the return is alone
  inside a statement-group (like an ``if`` statement);

* Use ``return null;`` when a function explicitly returns ``null`` values and
  use ``return;`` when the function returns ``void`` values;

* Use braces to indicate control structure body regardless of the number of
  statements it contains;

* Define one class per file - this does not apply to private helper classes
  that are not intended to be instantiated from the outside and thus are not
  concerned by the `PSR-0`_ and `PSR-4`_ autoload standards;

* Declare the class inheritance and all the implemented interfaces on the same
  line as the class name;

* Declare class properties before methods;

* Declare public methods first, then protected ones and finally private ones.
  The exceptions to this rule are the class constructor and the ``setUp()`` and
  ``tearDown()`` methods of PHPUnit tests, which must always be the first methods
  to increase readability;

* Declare all the arguments on the same line as the method/function name, no
  matter how many arguments there are;

* Use parentheses when instantiating classes regardless of the number of
  arguments the constructor has;

* Exception and error message strings must be concatenated using :phpfunction:`sprintf`;

* Calls to :phpfunction:`trigger_error` with type ``E_USER_DEPRECATED`` must be
  switched to opt-in via ``@`` operator.
  Read more at :ref:`contributing-code-conventions-deprecations`;

* Do not use ``else``, ``elseif``, ``break`` after ``if`` and ``case`` conditions
  which return or throw something;

* Do not use spaces around ``[`` offset accessor and before ``]`` offset accessor;

* Add a ``use`` statement for every class that is not part of the global namespace;

* When PHPDoc tags like ``@param`` or ``@return`` include ``null`` and other
  types, always place ``null`` at the end of the list of types.

Naming Conventions
~~~~~~~~~~~~~~~~~~

* Use `camelCase`_ for PHP variables, function and method names, arguments
  (e.g. ``$acceptableContentTypes``, ``hasSession()``);

* Use `snake_case`_ for configuration parameters and Twig template variables
  (e.g. ``framework.csrf_protection``, ``http_status_code``);

* Use namespaces for all PHP classes and `UpperCamelCase`_ for their names (e.g.
  ``ConsoleLogger``);

* Prefix all abstract classes with ``Abstract`` except PHPUnit ``*TestCase``.
  Please note some early Symfony classes do not follow this convention and
  have not been renamed for backward compatibility reasons. However all new
  abstract classes must follow this naming convention;

* Suffix interfaces with ``Interface``;

* Suffix traits with ``Trait``;

* Suffix exceptions with ``Exception``;

* Use UpperCamelCase for naming PHP files (e.g. ``EnvVarProcessor.php``) and
  snake case for naming Twig templates and web assets (``section_layout.html.twig``,
  ``index.scss``);

* For type-hinting in PHPDocs and casting, use ``bool`` (instead of ``boolean``
  or ``Boolean``), ``int`` (instead of ``integer``), ``float`` (instead of
  ``double`` or ``real``);

* Don't forget to look at the more verbose :doc:`conventions` document for
  more subjective naming considerations.

.. _service-naming-conventions:

Service Naming Conventions
~~~~~~~~~~~~~~~~~~~~~~~~~~

* A service name must be the same as the fully qualified class name (FQCN) of
  its class (e.g. ``App\EventSubscriber\UserSubscriber``);

* If there are multiple services for the same class, use the FQCN for the main
  service and use lowercased and underscored names for the rest of services.
  Optionally divide them in groups separated with dots (e.g.
  ``something.service_name``, ``fos_user.something.service_name``);

* Use lowercase letters for parameter names (except when referring
  to environment variables with the ``%env(VARIABLE_NAME)%`` syntax);

* Add class aliases for public services (e.g. alias ``Symfony\Component\Something\ClassName``
  to ``something.service_name``).

Documentation
~~~~~~~~~~~~~

* Add PHPDoc blocks for all classes, methods, and functions (though you may
  be asked to remove PHPDoc that do not add value);

* Group annotations together so that annotations of the same type immediately
  follow each other, and annotations of a different type are separated by a
  single blank line;

* Omit the ``@return`` tag if the method does not return anything;

* The ``@package`` and ``@subpackage`` annotations are not used;

* Don't inline PHPDoc blocks, even when they contain just one tag (e.g. don't
  put ``/** {@inheritdoc} */`` in a single line);

* When adding a new class or when making significant changes to an existing class,
  an ``@author`` tag with personal contact information may be added, or expanded.
  Please note it is possible to have the personal contact information updated or
  removed per request to the doc:`core team </contributing/code/core_team>`.

License
~~~~~~~

* Symfony is released under the MIT license, and the license block has to be
  present at the top of every PHP file, before the namespace.

.. _`PHP CS Fixer tool`: http://cs.sensiolabs.org/
.. _`PSR-0`: https://www.php-fig.org/psr/psr-0/
.. _`PSR-1`: https://www.php-fig.org/psr/psr-1/
.. _`PSR-2`: https://www.php-fig.org/psr/psr-2/
.. _`PSR-4`: https://www.php-fig.org/psr/psr-4/
.. _`identical comparison`: https://php.net/manual/en/language.operators.comparison.php
.. _`Yoda conditions`: https://en.wikipedia.org/wiki/Yoda_conditions
.. _`camelCase`: https://en.wikipedia.org/wiki/Camel_case
.. _`UpperCamelCase`: https://en.wikipedia.org/wiki/Camel_case
.. _`snake_case`: https://en.wikipedia.org/wiki/Snake_case
RST;

        yield [
            false,
            new RstSample($invalid, 265),
            RstParser::DIRECTIVE_CODE_BLOCK,
        ];

        yield [
            false,
            new RstSample($invalid, 265),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_PHP, RstParser::CODE_BLOCK_PHP_ANNOTATIONS],
        ];

        yield [
            true,
            new RstSample([
                '.. code-block:: php',
                '',
                '    /*',
                '     * {@inheritdoc}',
            ], 3),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_PHP, RstParser::CODE_BLOCK_PHP_ANNOTATIONS],
        ];

        yield [
            true,
            new RstSample([
                '.. code-block:: php-attributes',
                '',
                '    /*',
                '     * {@inheritdoc}',
            ], 3),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_PHP, RstParser::CODE_BLOCK_PHP_ATTRIBUTES],
        ];

        yield [
            true,
            new RstSample([
                '.. code-block:: php-symfony',
                '',
                '    /*',
                '     * {@inheritdoc}',
            ], 3),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_PHP, RstParser::CODE_BLOCK_PHP_SYMFONY],
        ];

        yield [
            true,
            new RstSample([
                '.. code-block:: php-standalone',
                '',
                '    /*',
                '     * {@inheritdoc}',
            ], 3),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_PHP, RstParser::CODE_BLOCK_PHP_STANDALONE],
        ];
    }

    /**
     * @test
     *
     * @group temp
     *
     * @dataProvider previousDirectiveIsProvider
     */
    public function previousDirectiveIs(bool $expected, RstSample $sample, string $directive, ?array $types = null): void
    {
        static::assertSame(
            $expected,
            $this->traitWrapper->previousDirectiveIs($directive, $sample->lines(), $sample->lineNumber(), $types)
        );
    }

    /**
     * @return \Generator<array{0: bool, 1: RstSample}>
     */
    public function previousDirectiveIsProvider(): \Generator
    {
        yield [
            false,
            new RstSample('I am just a cool text!'),
            RstParser::DIRECTIVE_CODE_BLOCK,
        ];

        yield [
            false,
            new RstSample(<<<RST
.. code-block:: php

    // I am just a cool text!
RST
                , 2),
            RstParser::DIRECTIVE_CODE_BLOCK,
        ];

        yield [
            true,
            new RstSample(<<<RST
.. code-block:: php

    // I am just a cool text!
    
.. code-block:: yaml
RST
                , 4),
            RstParser::DIRECTIVE_CODE_BLOCK,
            [RstParser::CODE_BLOCK_PHP],
        ];

        yield [
            true,
            new RstSample(<<<RST
.. code-block:: php

    // I am just a cool text!
    
.. code-block:: yaml
RST
                , 4),
            RstParser::DIRECTIVE_CODE_BLOCK,
        ];

        yield [
            true,
            new RstSample(<<<RST
.. configuration-block::

    .. code-block: yaml

        // I am just a cool text!

.. code-block:: php
RST
                , 6),
            RstParser::DIRECTIVE_CONFIGURATION_BLOCK,
        ];
    }
}
