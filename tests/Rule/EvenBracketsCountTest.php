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
            (new EvenBracketsCount())->check($sample->getContent(), $sample->getLineNumber())
        );
    }

    public function validProvider()
    {
        return [
            [
                null,
                new RstSample('this is a test'),
            ],
            [
                null,
                new RstSample([
                    '{this is a test',
                    '',
                    'foo }',
                ]),
            ],
            [
                null,
                new RstSample('((this is a test))'),
            ],
            [
                null,
                new RstSample('(this is a test)'),
            ],
            [
                null,
                new RstSample('{{this is a test}}'),
            ],
            [
                null,
                new RstSample('{this is a test}'),
            ],
            [
                null,
                new RstSample('[[this is a test]]'),
            ],
            [
                null,
                new RstSample('[this is a test]'),
            ],
//            [
//                null,
//                new RstSample('<<this is a test>>'),
//            ],
//            [
//                null,
//                new RstSample('<this is a test>'),
//            ],
//            [
//                null,
//                new RstSample("return ['response' => ['onResponse', -255]];"),
//            ],
            [
                null,
                new RstSample("Are you deploying to a CDN? That's awesome :) Once you've made sure that your"),
            ],
            [
                null,
                new RstSample("Are you NOT deploying to a CDN? That's NOT awesome :( Once you've made sure that your"),
            ],
            [
                null,
                new RstSample([
                    '',
                    '1) Create a Procfile',
                    '~~~~~~~~~~~~~~~~~~~~',
                    '',
                    'By default, Heroku will launch an',
                ]),
            ],
            [
                null,
                new RstSample([
                    '1) Create a Procfile',
                    '~~~~~~~~~~~~~~~~~~~~',
                    '',
                    'By default, Heroku will launch an',
                ]),
            ],
            [
                null,
                new RstSample([
                    '',
                    '2) Updating your Code to Work with the new Version',
                    '--------------------------------------------------',
                    '',
                    'By default, Heroku will launch an',
                ]),
            ],
            [
                null,
                new RstSample([
                    '2) Updating your Code to Work with the new Version',
                    '--------------------------------------------------',
                    '',
                    'By default, Heroku will launch an',
                ]),
            ],
            [
                null,
                new RstSample('* `A) Add Composer Dependencies`_'),
            ],
            [
                null,
                new RstSample('* A) Add Composer Dependencies`_'),
            ],
            [
                null,
                new RstSample('Step 5) Cleanup'),
            ],
//            [
//                null,
//                new RstSample('$container->autowire(Rot13Transformer::class);'),
//            ],
//            [
//                null,
//                new RstSample([
//                    '$container->register(\'matcher\', Routing\Matcher\UrlMatcher::class)',
//                    '    ->setArguments([\'%routes%\', new Reference(\'context\')])',
//                    ';',
//                ]),
//            ],
//            [
//                null,
//                new RstSample('$framework->handle($request)->send();'),
//            ],
//            [
//                null,
//                new RstSample('if (time() - strtotime($created) > 300) {}'),
//            ],
//            [
//                null,
//                new RstSample('if (time() - strtotime($created) < 300) {}'),
//            ],
//            [
//                null,
//                new RstSample('if (time() - strtotime($created) >= 300) {}'),
//            ],
//            [
//                null,
//                new RstSample('if (time() - strtotime($created) =< 300) {}'),
//            ],
//            [
//                null,
//                new RstSample('if (time() - strtotime($created) =< $var) {}'),
//            ],
//            [
//                null,
//                new RstSample('return $value <= new \DateTime(\'+3 days\');')
//            ],
//            [
//                null,
//                new RstSample('its dedicated :doc:`documentation </components/http_foundation>`.'),
//            ],
//            [
//                null,
//                new RstSample('$name = $request->get(\'name\', \'World\');'),
//            ],
//            [
//                null,
//                new RstSample('$response->send();'),
//            ],
//            [
//                null,
//                new RstSample('$this->key = $key;'),
//            ],
//            [
//                null,
//                new RstSample(':ref:`role hierarchy <security-role-hierarchy>` but not including the'),
//            ],
//            [
//                null,
/*                new RstSample('<?php endif ?>'),*/
//            ],
//            [
//                null,
//                new RstSample('<?php'),
//            ],
//            [
//                null,
/*                new RstSample('?>'),*/
//            ],
//            [
//                null,
//                new RstSample('Hello <?= htmlspecialchars($name)'),
//            ],
//            [
//                null,
//                new RstSample('<?xml version="1.0" encoding="UTF-8"'),
//            ],
//            [
//                null,
//                new RstSample('$errorMessage = $errors[0]->getMessage();'),
//            ],
//            [
//                null,
//                new RstSample('-----> PHP app detected'),
//            ]
        ];
    }

    public function invalidProvider()
    {
        return [
            [
                'Please make sure you have even number of brackets in the file!',
                new RstSample([
                    'this is a test',
                    ')',
                ]),
            ],
            [
                'Please make sure you have even number of brackets in the file!',
                new RstSample([
                    '({this is a test',
                    ')',
                ]),
            ],
//            [
//                'Please make sure you have even number of brackets in the file!',
//                new RstSample('< =>'),
//            ],
//            [
//                'Please make sure you have even number of brackets in the file!',
//                new RstSample([
//                    '<!-- example.com/src/pages/hello.php -->',
//                    'test >',
//                ]),
//            ],
        ];
    }

    public function realSymfonyFileProvider()
    {
        return [
            [
                null,
                new RstSample(file_get_contents(__DIR__.'/../Fixtures/testfile.rst')),
            ],
        ];
    }
}
