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

use App\Rule\NoDuplicateUseStatements;
use App\Tests\RstSample;
use App\Tests\UnitTestCase;
use App\Value\NullViolation;
use App\Value\Violation;
use App\Value\ViolationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NoDuplicateUseStatementsTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('checkProvider')]
    public function check(ViolationInterface $expected, RstSample $sample): void
    {
        self::assertEquals(
            $expected,
            (new NoDuplicateUseStatements())->check($sample->lines, $sample->lineNumber, 'filename'),
        );
    }

    public static function checkProvider(): iterable
    {
        $codeBlocks = self::phpCodeBlocks();

        // VALID
        foreach ($codeBlocks as $codeBlock) {
            // WITH blank line after directive
            yield [
                NullViolation::create(),
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                NullViolation::create(),
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
                Violation::from(
                    'Please remove duplication of "use Symfony\A;"',
                    'filename',
                    1,
                    $codeBlock,
                ),
                new RstSample([
                    $codeBlock,
                    '',
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];

            // WITHOUT blank line after directive
            yield [
                Violation::from(
                    'Please remove duplication of "use Symfony\A;"',
                    'filename',
                    1,
                    $codeBlock,
                ),
                new RstSample([
                    $codeBlock,
                    '    use Symfony\A;',
                    '    use Symfony\B;',
                    '    use Symfony\A;',
                ]),
            ];
        }

        $valid_with_two_code_examples_in_one_block = <<<'RST'
.. code-block:: php

    // src/Repository/DoctrineUserRepository.php
    namespace App\Repository;

    use App\Repository\BaseDoctrineRepository;

    // ...
    class DoctrineUserRepository extends BaseDoctrineRepository
    {
        // ...
    }

    // src/Repository/DoctrinePostRepository.php
    namespace App\Repository;

    use App\Repository\BaseDoctrineRepository;

    // ...
    class DoctrinePostRepository extends BaseDoctrineRepository
    {
        // ...
    }

The service container allows you to extend parent services in order to
RST;

        yield 'valid without class but variable in between' => [
            NullViolation::create(),
            new RstSample($valid_with_two_code_examples_in_one_block, 0),
        ];
    }
}
