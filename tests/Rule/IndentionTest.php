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

use App\Rule\Indention;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class IndentionTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, int $size, RstSample $sample): void
    {
        $rule = (new Indention());
        $rule->setOptions(['size' => $size]);

        self::assertEquals($expected, $rule->check($sample->lines, $sample->lineNumber, 'filename'));
    }

    public static function checkProvider(): iterable
    {
        yield [NullViolation::create(), 4, new RstSample('')];
        yield [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
Headline

    Content
RST
                , 2),
        ];

        yield [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
Headline
Content
RST
                , 1),
        ];

        yield [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
Headline

RST
                , 1),
        ];

        yield 'wrong without blank line' => [
            Violation::from(
                'Please add 4 spaces for every indention.',
                'filename',
                3,
                'Content',
            ),
            4,
            new RstSample(<<<'RST'
Headline
========
  Content
RST
                , 2),
        ];

        yield 'wrong with blank line' => [
            Violation::from(
                'Please add 4 spaces for every indention.',
                'filename',
                4,
                'Content',
            ),
            4,
            new RstSample(<<<'RST'
Headline
========

  Content
RST
                , 3),
        ];

        yield [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
.. index::
   single: Cache

HTTP Cache
==========

The nature of rich web applications means that they're dynamic. No matter
RST
                , 1),
        ];

        $php_comment_example = <<<'RST'
Code here::

    final class MicroController extends Controller
    {
        /**
         * @Route("/random/{limit}")
         */
        public function randomAction($limit)
        {
RST;

        yield 'first line of the php comment' => [
            NullViolation::create(),
            4,
            new RstSample($php_comment_example, 4),
        ];

        yield 'middle of the php comment' => [
            NullViolation::create(),
            4,
            new RstSample($php_comment_example, 5),
        ];

        yield 'last line of the php comment' => [
            NullViolation::create(),
            4,
            new RstSample($php_comment_example, 6),
        ];

        yield 'wrong indention in php DocBlock' => [
            Violation::from(
                'Please fix the indention of the PHP DocBlock.',
                'filename',
                6,
                '* @Assert\NotBlank',
            ),
            4,
            new RstSample(<<<'RST'
Code here::

    class User
    {
        /**
        * @Assert\NotBlank
        */
        protected $name;
    }
RST
                , 5),
        ];

        yield 'valid multiline php comment' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
Code here::

    $types = $propertyInfo->getTypes($class, $property);
    /*
        Example Result
        --------------
        array(1) {
            private $collectionValueType  => NULL
        }
    */
RST
                , 4),
        ];

        yield 'valid multiline php comment 2' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
Code here::

    $types = $propertyInfo->getTypes($class, $property);
    /*
        Example Result
        --------------
        array(1) {
            private $collectionValueType  => NULL
        }
    */
RST
                , 9),
        ];

        yield 'valid multiline php comment 3' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
Code here::

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (true !== false) {
RST
                , 7),
        ];

        yield 'list item (#) first line' => [
            NullViolation::create(),
            4,
            new RstSample(
                <<<'RST'
#. At the beginning of the request, the Firewall checks the firewall map
   to see if any firewall should be active for this URL;
RST
            ),
        ];

        yield 'list item (#) second line' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
#. At the beginning of the request, the Firewall checks the firewall map
   to see if any firewall should be active for this URL;
RST
                , 1),
        ];

        yield 'list item (*) first line' => [
            NullViolation::create(),
            4,
            new RstSample(
                <<<'RST'
* At the beginning of the request, the Firewall checks the firewall map
  to see if any firewall should be active for this URL;
RST
            ),
        ];

        yield 'list item (*) second line' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
* At the beginning of the request, the Firewall checks the firewall map
  to see if any firewall should be active for this URL;
RST
                , 1),
        ];

        yield 'comment (rst) first line' => [
            NullViolation::create(),
            4,
            new RstSample(
                <<<'RST'
.. I am a comment
   and have a second line.
RST
            ),
        ];

        yield 'comment (rst) second line' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
.. I am a comment
   and have a second line.
RST
                , 1),
        ];

        yield 'special char "├─"' => [
            NullViolation::create(),
            4,
            new RstSample(
                <<<'RST'
  ├─ app.php
RST
            ),
        ];

        yield 'special char "└─"' => [
            NullViolation::create(),
            4,
            new RstSample(
                <<<'RST'
  └─ ...
RST
            ),
        ];

        yield 'twig multiline comment' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
.. code-block:: twig

    {# if the controller is associated with a route, use the path() or
        url() functions to generate the URI used by render() #}
RST
                , 3),
        ];

        yield 'twig multiline comment on second level' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
Info here:

    .. code-block:: twig

        {# if the controller is associated with a route, use the path() or
            url() functions to generate the URI used by render() #}
RST
                , 5),
        ];

        yield 'xml multiline comment' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
.. code-block:: xml

    <services>
        <!-- ... -->

        <!-- overrides the public setting of the parent service -->
        <service id="AppBundle\Repository\DoctrineUserRepository"
            parent="AppBundle\Repository\BaseDoctrineRepository"
            public="false"
        >
            <!-- appends the '@app.username_checker' argument to the parent
                 argument list -->
RST
                , 11),
        ];

        yield 'yaml array' => [
            NullViolation::create(),
            4,
            new RstSample(<<<'RST'
.. code-block:: yaml

    # ...
    folders:
        - map: ~/projects
          to: /home/vagrant/projects
RST
                , 5),
        ];
    }

    #[Test]
    #[DataProvider('multilineXmlProvider')]
    public function isPartOfMultilineXmlComment(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            (new Indention())->isPartOrMultilineXmlComment($sample->lines, $sample->lineNumber),
        );
    }

    public static function multilineXmlProvider(): iterable
    {
        yield [
            true,
            new RstSample(
                <<<'RST'
<!-- appends the '@app.username_checker' argument to the parent
     argument list -->
RST
            ),
        ];

        yield [
            true,
            new RstSample(<<<'RST'
<!-- appends the '@app.username_checker' argument to the parent
     argument list -->
RST
                , 1),
        ];

        yield [
            false,
            new RstSample(
                <<<'RST'
<!-- appends the '@app.username_checker' argument to the parent -->
RST
            ),
        ];

        yield [
            false,
            new RstSample(<<<'RST'
<!-- call a method on the specified factory service -->
<factory service="AppBundle\Email\NewsletterManagerFactory"
    method="createNewsletterManager"
/>
RST
                , 2),
        ];

        yield [
            false,
            new RstSample(<<<'RST'
<monolog:config>
    <!--
    500 errors are logged at the critical level,
    to also log 400 level errors (but not 404's):
    action-level="error"
    And add this child inside this monolog:handler
    <monolog:excluded-404>^/</monolog:excluded-404>
    -->
    <monolog:handler
        name="main"
RST
                , 9),
        ];

        yield [false, new RstSample('foo bar')];
    }

    #[Test]
    #[DataProvider('multilineTwigProvider')]
    public function isPartOfMultilineTwigComment(bool $expected, RstSample $sample): void
    {
        self::assertSame(
            $expected,
            (new Indention())->isPartOrMultilineTwigComment($sample->lines, $sample->lineNumber),
        );
    }

    public static function multilineTwigProvider(): iterable
    {
        yield [
            true,
            new RstSample(
                <<<'RST'
{# appends the '@app.username_checker' argument to the parent
   argument list #}
RST
            ),
        ];

        yield [
            true,
            new RstSample(<<<'RST'
{# appends the '@app.username_checker' argument to the parent
   argument list #}
RST
                , 1),
        ];

        yield [
            false,
            new RstSample(
                <<<'RST'
{# appends the '@app.username_checker' argument to the parent #}
RST
            ),
        ];

        yield [false, new RstSample('foo bar')];
    }
}
