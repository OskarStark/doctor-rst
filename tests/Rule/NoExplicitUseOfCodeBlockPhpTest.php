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

use App\Rule\NoExplicitUseOfCodeBlockPhp;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class NoExplicitUseOfCodeBlockPhpTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     * @dataProvider realSymfonyFileProvider
     */
    public function check($expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new NoExplicitUseOfCodeBlockPhp())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function checkProvider()
    {
        return [
            [
                null,
                new RstSample('Check the following controller syntax::'),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample('.. code-block:: php'),
            ],
            [
                null,
                new RstSample('.. code-block:: html+php'),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample('    .. code-block:: php'),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample([
                    'Welcome to our tutorial!',
                    '',
                    '     .. code-block:: php',
                    '',
                    'namespace App\Entity;',
                ], 2),
            ],
            [
                null,
                new RstSample([
                    '.. configuration-block::',
                    '',
                    ' .. code-block:: php',
                    '',
                    '  namespace App\Entity;',
                ], 2),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample([
                    '    .. configuration-block::',
                    '',
                    '        .. code-block:: xml',
                    '',
                    '            <!-- foo-bar -->',
                    '',
                    '    .. code-block:: php',
                    '',
                    'namespace App\Entity;',
                ], 6),
            ],
        ];
    }

    public function realSymfonyFileProvider()
    {
        $content = <<<'CONTENT'
.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.mailer:
                class:        AppBundle\Mailer
                arguments:    [sendmail]

    .. code-block:: xml

        <!-- app/config/services.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services
                http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>
                <service id="app.mailer" class="AppBundle\Mailer">
                    <argument>sendmail</argument>
                </service>
            </services>
        </container>

    .. code-block:: php

        // app/config/services.php
        use AppBundle\Mailer;

        $container->register('app.mailer', Mailer::class)
            ->addArgument('sendmail');

CONTENT;

        $content_with_blank_line_at_the_beginning = <<<'CONTENT'

.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.mailer:
                class:        AppBundle\Mailer
                arguments:    [sendmail]

    .. code-block:: xml

        <!-- app/config/services.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services
                http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>
                <service id="app.mailer" class="AppBundle\Mailer">
                    <argument>sendmail</argument>
                </service>
            </services>
        </container>

    .. code-block:: php

        // app/config/services.php
        use AppBundle\Mailer;

        $container->register('app.mailer', Mailer::class)
            ->addArgument('sendmail');

CONTENT;

        $invalid_content = <<<CONTENT
.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            app.mailer:
                class:        AppBundle\Mailer
                arguments:    [sendmail]

.. note::

    Try to use it like this:

    .. code-block:: php

        echo 'foo';

CONTENT;

        $valid_code_block_after_headline = <<<'CONTENT'
Creating an ACL and Adding an ACE
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // src/AppBundle/Controller/BlogController.php
    namespace AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Security\Core\Exception\AccessDeniedException;
    use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
    use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
    use Symfony\Component\Security\Acl\Permission\MaskBuilder;

    class BlogController extends Controller
    {
CONTENT;

        return [
            [
                null,
                new RstSample($content, 26),
            ],
            [
                null,
                new RstSample($content_with_blank_line_at_the_beginning, 27),
            ],
            [
                'Please do not use ".. code-block:: php", use "::" instead.',
                new RstSample($invalid_content, 14),
            ],
            [
                null,
                new RstSample($valid_code_block_after_headline, 3),
            ],
        ];
    }
}
