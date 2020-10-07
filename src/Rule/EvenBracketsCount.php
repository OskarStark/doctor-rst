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

namespace App\Rule;

use App\Value\Lines;
use App\Value\RuleGroup;

class EvenBracketsCount extends AbstractRule implements Rule
{
    private int $round = 0;
    private int $curly = 0;
    private int $square = 0;
    ///** @var int */
    //private $edge = 0;

    public static function getType(): int
    {
        return Rule::TYPE_FILE;
    }

    public static function getGroups(): array
    {
        return [RuleGroup::Experimental()];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $this->round = 0;
        $this->curly = 0;
        $this->square = 0;
        //$this->edge = 0;

        $lines->seek($number);

        while ($lines->valid()) {
            if (preg_match_all('/(\(|\))/', $lines->current()->raw(), $matches)) {
                $this->round = $this->round + \count($matches[0]);

                // allow smilies
                if (preg_match_all('/(\:\)|\:\()/', $lines->current()->raw(), $matches)) {
                    $this->round = $this->round - \count($matches[0]);
                }

                // allow sth like: "1) Chapter" or "A) Chapter"
                // and: * `A) Chapter`
                if (preg_match_all('/^(\* (\`)?|Step )?[a-zA-Z0-9]\)/', $lines->current()->raw(), $matches)) {
                    $this->round = $this->round - \count($matches[0]);
                }
            }

            if (preg_match_all('/(\{|\})/', $lines->current()->raw(), $matches)) {
                $this->curly = $this->curly + \count($matches[0]);
            }

            if (preg_match_all('/(\[|\])/', $lines->current()->raw(), $matches)) {
                $this->square = $this->square + \count($matches[0]);
            }
//
//            if (preg_match_all('/(<|>)/', $lines->current(), $matches)) {
//                $this->edge = $this->edge + \count($matches[0]);
//
//                // allow "=>" for arrays
//                $pattern1 = '=>';
//
//                // allow "->" for calls like: $container->setFoo(...)
//                $pattern2 = '[a-z\s\)\]]->';
//
//                // allow comparisons like: <, >, >=, =<, <=
//                $pattern3 = '[a-z\)] (>=?|<(>)?|=<|<=) ([\$0-9]|[a-z])';
//
//                // allow <?php and >
            /*                $pattern4 = '(<\?(php|xml)|<\?=|\?>)';*/
//
//                $pattern5 = '----->';
//
//                if (preg_match_all(sprintf(
//                    '/(%s)|(%s)|(%s)|(%s)|(%s)/',
//                    $pattern1,
//                    $pattern2,
//                    $pattern3,
//                    $pattern4,
//                    $pattern5
//                ), $lines->current(), $matches)) {
//                    $this->edge = $this->edge - \count($matches[0]);
//
//                    #dump($matches);
//                }
//            }

            $lines->next();
        }

        if ((0 < $this->round && 0 !== ($this->round % 2))
            || (0 < $this->curly && 0 !== ($this->curly % 2))
            || (0 < $this->square && 0 !== ($this->square % 2))
            //|| (0 < $this->edge && 0 !== ($this->edge % 2))
        ) {
            return 'Please make sure you have even number of brackets in the file!';
        }

        return null;
    }
}
