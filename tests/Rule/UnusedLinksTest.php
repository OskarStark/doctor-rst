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

use App\Rule\UnusedLinks;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class UnusedLinksTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('invalidProvider')]
    #[DataProvider('validProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new UnusedLinks())->check($sample->lines, 'filename'),
        );
    }

    public static function validProvider(): iterable
    {
        yield [
            NullViolation::create(),
            new RstSample('this is a test'),
        ];

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'RST'
I am a `Link`_

.. _`Link`: https://example.com
RST
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'RST'
I am a `Link`_ and `Link2`_

.. _`Link`: https://example.com
.. _`Link2`: https://example2.com
RST
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'RST'
I am a `Link`_

.. _Link: https://example.com
RST
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'RST'
I am a `Link`_ and `Link2`_

.. _Link: https://example.com
.. _Link2: https://example2.com
RST
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'RST'
I am `a Link`_, `some other Link`_ and Link2_

.. _a Link: https://example.com
.. _Link2: https://example2.com
.. _`some other Link`: https://example3.com
RST
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'RST'
Date Handling
~~~~~~~~~~~~~

By default, the YAML parser will convert unquoted strings which look like a
date or a date-time into a Unix timestamp; for example ``2016-05-27`` or
``2016-05-27T02:59:43.1Z`` (`ISO-8601`_)::

.. _`ISO-8601`: http://www.iso.org/iso/iso8601
RST
            ),
        ];

        yield [
            NullViolation::create(),
            new RstSample(
                <<<'RST'
Active Core Members
~~~~~~~~~~~~~~~~~~~

* **Project Leader**:

  * **Fabien Potencier** (`fabpot`_).

* **Mergers Team** (``@symfony/mergers`` on GitHub):

  * **Nicolas Grekas** (`nicolas-grekas`_);
  * **Christophe Coevoet** (`stof`_);
  * **Christian Flothmann** (`xabbuh`_);
  * **Tobias Schultze** (`Tobion`_);
  * **Kévin Dunglas** (`dunglas`_);
  * **Jakub Zalas** (`jakzal`_);
  * **Javier Eguiluz** (`javiereguiluz`_);
  * **Grégoire Pineau** (`lyrixx`_);
  * **Ryan Weaver** (`weaverryan`_);
  * **Robin Chalas** (`chalasr`_);
  * **Maxime Steinhausser** (`ogizanagi`_);
  * **Samuel Rozé** (`sroze`_);
  * **Yonel Ceruto** (`yceruto`_).

* **Security Team** (``@symfony/security`` on GitHub):

  * **Fabien Potencier** (`fabpot`_);
  * **Michael Cullum** (`michaelcullum`_).

* **Recipes Team**:

  * **Fabien Potencier** (`fabpot`_);
  * **Tobias Nyholm** (`Nyholm`_).

* **Documentation Team** (``@symfony/team-symfony-docs`` on GitHub):

  * **Fabien Potencier** (`fabpot`_);
  * **Ryan Weaver** (`weaverryan`_);
  * **Christian Flothmann** (`xabbuh`_);
  * **Wouter De Jong** (`wouterj`_);
  * **Jules Pietri** (`HeahDude`_);
  * **Javier Eguiluz** (`javiereguiluz`_).
  * **Oskar Stark** (`OskarStark`_).

Former Core Members
~~~~~~~~~~~~~~~~~~~

They are no longer part of the core team, but we are very grateful for all their
Symfony contributions:

* **Bernhard Schussek** (`webmozart`_);
* **Abdellatif AitBoudad** (`aitboudad`_);
* **Romain Neutron** (`romainneutron`_);
* **Jordi Boggiano** (`Seldaek`_).

.. _`fabpot`: https://github.com/fabpot/
.. _`webmozart`: https://github.com/webmozart/
.. _`Tobion`: https://github.com/Tobion/
.. _`nicolas-grekas`: https://github.com/nicolas-grekas/
.. _`stof`: https://github.com/stof/
.. _`dunglas`: https://github.com/dunglas/
.. _`jakzal`: https://github.com/jakzal/
.. _`Seldaek`: https://github.com/Seldaek/
.. _`weaverryan`: https://github.com/weaverryan/
.. _`aitboudad`: https://github.com/aitboudad/
.. _`xabbuh`: https://github.com/xabbuh/
.. _`javiereguiluz`: https://github.com/javiereguiluz/
.. _`lyrixx`: https://github.com/lyrixx/
.. _`chalasr`: https://github.com/chalasr/
.. _`ogizanagi`: https://github.com/ogizanagi/
.. _`Nyholm`: https://github.com/Nyholm
.. _`sroze`: https://github.com/sroze
.. _`yceruto`: https://github.com/yceruto
.. _`michaelcullum`: https://github.com/michaelcullum
.. _`wouterj`: https://github.com/wouterj
.. _`HeahDude`: https://github.com/HeahDude
.. _`OskarStark`: https://github.com/OskarStark
.. _`romainneutron`: https://github.com/romainneutron
RST
            ),
        ];
    }

    public static function invalidProvider(): iterable
    {
        yield [
            Violation::from(
                'The following link definitions aren\'t used anymore and should be removed: "unused"',
                'filename',
                1,
                '',
            ),
            new RstSample(
                <<<'RST'
I am a `Link`_

.. _`Link`: https://example.com
.. _`unused`: https://404.com
RST
            ),
        ];

        yield [
            Violation::from(
                'The following link definitions aren\'t used anymore and should be removed: "unused"',
                'filename',
                1,
                '',
            ),
            new RstSample(
                <<<'RST'
I am a `Link`_

.. _Link: https://example.com
.. _unused: https://404.com
RST
            ),
        ];

        yield [
            Violation::from(
                'The following link definitions aren\'t used anymore and should be removed: "unused2", "unused1", "unused 3"',
                'filename',
                1,
                '',
            ),
            new RstSample(
                <<<'RST'
I am a `Link`_

.. _unused2: https://example.com/foo
.. _Link: https://example.com
.. _unused1: https://404.com
.. _`unused 3`: https://example.org
RST
            ),
        ];
    }
}
