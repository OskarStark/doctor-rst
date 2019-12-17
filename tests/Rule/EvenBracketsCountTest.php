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

use App\Rule\EvenBracketsCount;
use App\Tests\RstSample;
use PHPUnit\Framework\TestCase;

class EvenBracketsCountTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validProvider
     * @dataProvider invalidProvider
     * @dataProvider realSymfonyFileProvider
     */
    public function check(?string $expected, RstSample $sample)
    {
        $this->assertSame(
            $expected,
            (new EvenBracketsCount())->check($sample->lines(), $sample->lineNumber())
        );
    }

    /**
     * @return \Generator<array{0: null, 1: RstSample}>
     */
    public function validProvider(): \Generator
    {
        yield [
            null,
            new RstSample('this is a test'),
        ];
        yield [
            null,
            new RstSample([
                '{this is a test',
                '',
                'foo }',
            ]),
        ];
        yield [
            null,
            new RstSample('((this is a test))'),
        ];
        yield [
            null,
            new RstSample('(this is a test)'),
        ];
        yield [
            null,
            new RstSample('{{this is a test}}'),
        ];
        yield [
            null,
            new RstSample('{this is a test}'),
        ];
        yield [
            null,
            new RstSample('[[this is a test]]'),
        ];
        yield [
            null,
            new RstSample('[this is a test]'),
        ];
//        yield [
//            null,
//            new RstSample('<<this is a test>>'),
//        ];
//        yield [
//            null,
//            new RstSample('<this is a test>'),
//        ];
//        yield [
//            null,
//            new RstSample("return ['response' => ['onResponse', -255]];"),
//        ];
        yield [
            null,
            new RstSample("Are you deploying to a CDN? That's awesome :) Once you've made sure that your"),
        ];
        yield [
            null,
            new RstSample("Are you NOT deploying to a CDN? That's NOT awesome :( Once you've made sure that your"),
        ];
        yield [
            null,
            new RstSample([
                '',
                '1) Create a Procfile',
                '~~~~~~~~~~~~~~~~~~~~',
                '',
                'By default, Heroku will launch an',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '1) Create a Procfile',
                '~~~~~~~~~~~~~~~~~~~~',
                '',
                'By default, Heroku will launch an',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '',
                '2) Updating your Code to Work with the new Version',
                '--------------------------------------------------',
                '',
                'By default, Heroku will launch an',
            ]),
        ];
        yield [
            null,
            new RstSample([
                '2) Updating your Code to Work with the new Version',
                '--------------------------------------------------',
                '',
                'By default, Heroku will launch an',
            ]),
        ];
        yield [
            null,
            new RstSample('* `A) Add Composer Dependencies`_'),
        ];
        yield [
            null,
            new RstSample('* A) Add Composer Dependencies`_'),
        ];
        yield [
            null,
            new RstSample('Step 5) Cleanup'),
        ];
//        yield [
//            null,
//            new RstSample('$container->autowire(Rot13Transformer::class);'),
//        ];
//        yield [
//            null,
//            new RstSample([
//                '$container->register(\'matcher\', Routing\Matcher\UrlMatcher::class)',
//                '    ->setArguments([\'%routes%\', new Reference(\'context\')])',
//                ';',
//            ]),
//        ];
//        yield [
//            null,
//            new RstSample('$framework->handle($request)->send();'),
//        ];
//        yield [
//            null,
//            new RstSample('if (time() - strtotime($created) > 300) {}'),
//        ];
//        yield [
//            null,
//            new RstSample('if (time() - strtotime($created) < 300) {}'),
//        ];
//        yield [
//            null,
//            new RstSample('if (time() - strtotime($created) >= 300) {}'),
//        ];
//        yield [
//            null,
//            new RstSample('if (time() - strtotime($created) =< 300) {}'),
//        ];
//        yield [
//            null,
//            new RstSample('if (time() - strtotime($created) =< $var) {}'),
//        ];
//        yield [
//            null,
//            new RstSample('return $value <= new \DateTime(\'+3 days\');')
//        ];
//        yield [
//            null,
//            new RstSample('its dedicated :doc:`documentation </components/http_foundation>`.'),
//        ];
//        yield [
//            null,
//            new RstSample('$name = $request->get(\'name\', \'World\');'),
//        ];
//        yield [
//            null,
//            new RstSample('$response->send();'),
//        ];
//        yield [
//            null,
//            new RstSample('$this->key = $key;'),
//        ];
//        yield [
//            null,
//            new RstSample(':ref:`role hierarchy <security-role-hierarchy>` but not including the'),
//        ];
//        yield [
//            null,
/*            new RstSample('<?php endif ?>'),*/
//        ];
//        yield [
//            null,
//            new RstSample('<?php'),
//        ];
//        yield [
//            null,
/*            new RstSample('?>'),*/
//        ];
//        yield [
//            null,
//            new RstSample('Hello <?= htmlspecialchars($name)'),
//        ];
//        yield [
//            null,
//            new RstSample('<?xml version="1.0" encoding="UTF-8"'),
//        ];
//        yield [
//            null,
//            new RstSample('$errorMessage = $errors[0]->getMessage();'),
//        ];
//        yield [
//            null,
//            new RstSample('-----> PHP app detected'),
//        ];
    }

    /**
     * @return \Generator<array{0: string, 1: RstSample}>
     */
    public function invalidProvider(): \Generator
    {
        yield [
            'Please make sure you have even number of brackets in the file!',
            new RstSample([
                'this is a test',
                ')',
            ]),
        ];
        yield [
            'Please make sure you have even number of brackets in the file!',
            new RstSample([
                '({this is a test',
                ')',
            ]),
        ];
//        yield [
//            'Please make sure you have even number of brackets in the file!',
//            new RstSample('< =>'),
//        ];
//        yield [
//            'Please make sure you have even number of brackets in the file!',
//            new RstSample([
//                '<!-- example.com/src/pages/hello.php -->',
//                'test >',
//            ]),
//        ];
    }

    /**
     * @return \Generator<array{0: null, 1: RstSample}>
     */
    public function realSymfonyFileProvider(): \Generator
    {
        yield [
            null,
            new RstSample(file_get_contents(__DIR__.'/../Fixtures/testfile.rst')),
        ];
    }
}
