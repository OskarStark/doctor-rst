<?php

namespace App\Rst;

/**
 * A reStructured Text tokenizer.
 *
 * This tokenizer is strongly inspired by the Python Pygments library (https://github.com/pygments/pygments)
 * Copyright (c) 2006 by the Pygments authors (see AUTHORS file).
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Tokenizer
{
    private $ast = [];

    const TYPE_WHITESPACE = 0;
    const TYPE_TITLE = 1;
    const TYPE_UL = 2;
    const TYPE_OL = 3;
    const TYPE_FIELDLIST = 4;
    const TYPE_DIRECTIVE = 5;
    const TYPE_TEXT = 6;
    const TYPE_LITERAL = 7;
    const TYPE_REFERENCE = 8;
    const TYPE_ROLE = 9;
    const TYPE_STRONG = 10;
    const TYPE_EM = 11;
    const TYPE_FOOTNOTE = 12;
    const TYPE_HYPERLINK = 13;
    const TYPE_LINEBLOCK = 14;
    const TYPE_REFERENCE_TARGET = 15;
    const TYPE_FOOTNOTE_TARGET = 16;
    const TYPE_SUBSTITUTION_DEF = 17;
    const TYPE_COMMENT = 18;
    const TYPE_DEFINITION_LIST = 19;
    const TYPE_CODE_BLOCK = 20;
    const TYPE_DIRECTIVE_TEXT = 21;
    
    public static $typeMap = [
        0 => 'TYPE_WHITESPACE',
        1 => 'TYPE_TITLE',
        2 => 'TYPE_UL',
        3 => 'TYPE_OL',
        4 => 'TYPE_FIELDLIST',
        5 => 'TYPE_DIRECTIVE',
        6 => 'TYPE_TEXT',
        7 => 'TYPE_LITERAL',
        8 => 'TYPE_REFERENCE',
        9 => 'TYPE_ROLE',
        10 => 'TYPE_STRONG',
        11 => 'TYPE_EM',
        12 => 'TYPE_FOOTNOTE',
        13 => 'TYPE_HYPERLINK',
        14 => 'TYPE_LINEBLOCK',
        15 => 'TYPE_REFERENCE_TARGET',
        16 => 'TYPE_FOOTNOTE_TARGET',
        17 => 'TYPE_SUBSTITUTION_DEF',
        18 => 'TYPE_COMMENT',
        19 => 'TYPE_DEFINITION_LIST',
        20 => 'TYPE_CODE_BLOCK',
        21 => 'TYPE_DIRECTIVE_TEXT',
    ];

    private static $tokens;
    private static $_tokens = [
        'document' => [
            // title with overline + underline
            [
                '^(\!+|"+|#+|\$+|%+|&+|\'+|\(+|\)+|\*+|\++|,+|-+|\.+|\/+|:+|;+|<+|=+|>+|\?+|@+|\[+|\\+|\]+|\^+|_+|`+|\{+|\|+|\}+|~+)([ \t]*\n)(.+)\n(\1)\n',
                [self::TYPE_TITLE, self::TYPE_WHITESPACE, self::TYPE_TITLE, self::TYPE_WHITESPACE, self::TYPE_TITLE, self::TYPE_WHITESPACE],
            ],

            // title with underline only
            [
                '^(\S.*)(\n)(\!{3,}|"{3,}|#{3,}|\${3,}|%{3,}|&{3,}|\'{3,}|\({3,}|\){3,}|\*{3,}|\+{3,}|,{3,}|-{3,}|\.{3,}|\/{3,}|:{3,}|;{3,}|<{3,}|={3,}|>{3,}|\?{3,}|@{3,}|\[{3,}|\\{3,}|\]{3,}|\^{3,}|_{3,}|`{3,}|\{{3,}|\|{3,}|\}{3,}|~+)(\n)',
                [self::TYPE_TITLE, self::TYPE_WHITESPACE, self::TYPE_TITLE, self::TYPE_WHITESPACE],
            ],

            // bullet list
            [
                '^(\s*)([-*+])( .+\n(?:\1  .+\n)*)',
                [self::TYPE_WHITESPACE, self::TYPE_UL, 'using:inline'],
            ],

            // numbered list
            [
                '^(\s*)([0-9#ivxlcmIVXLCM]+\.)( .+\n(?:\1  .+\n)*)',
                [self::TYPE_WHITESPACE, self::TYPE_OL, 'using:inline'],
            ],
            [
                '^(\s*)(\(?[0-9#ivxlcmIVXLCM]+\))( .+\n(?:\1  .+\n)*)',
                [self::TYPE_WHITESPACE, self::TYPE_OL, 'using:inline'],
            ],
            [
                '^(\s*)([A-Z]+\.)( .+\n(?:\1  .+\n)+)',
                [self::TYPE_WHITESPACE, self::TYPE_OL, 'using:inline'],
            ],
            [
                '^(\s*)(\(?[A-Za-z]+\))( .+\n(?:\1  .+\n)+)',
                [self::TYPE_WHITESPACE, self::TYPE_OL, 'using:inline'],
            ],

            // line block
            [
                '^(\s*)(\|)( .+\n(?:\|  .+\n)*)',
                [self::TYPE_WHITESPACE, self::TYPE_LINEBLOCK, 'using:inline'],
            ],

            // code block directive
            [
                '^( *)(\.\.\s*(?:source)?code(?:-block)?::)([ \t]*)([^\n]+)(\n[ \t]*\n)(\1[ \t]+)(.*)(\n)((?:(?:\6.*|)\n)+)',
                [self::TYPE_WHITESPACE, self::TYPE_DIRECTIVE, self::TYPE_WHITESPACE, self::TYPE_DIRECTIVE_TEXT, self::TYPE_WHITESPACE, self::TYPE_WHITESPACE, self::TYPE_DIRECTIVE_TEXT, self::TYPE_WHITESPACE, self::TYPE_DIRECTIVE_TEXT]
            ],

            // directive
            [
                '^( *\.\.\s*[\w:-]+?::)(?:([ \t]*)(.*))',
                [self::TYPE_DIRECTIVE, self::TYPE_WHITESPACE, 'using:inline'],
            ],

            // reference target
            [
                '^( *\.\.\s*_(?:[^\\:]|\\.)+:)(.*?)$',
                [self::TYPE_REFERENCE_TARGET, 'using:inline'],
            ],

            // footnote target
            [
                '^( *\.\.\s*\[.+\])(.*?)$',
                [self::TYPE_FOOTNOTE_TARGET, 'using:inline'],
            ],

            // substitution def
            [
                '^( *\.\.\s*\|.+\|\s*[\w:-]+?::)(?:([ \t]*)(.*))',
                [self::TYPE_SUBSTITUTION_DEF, self::TYPE_WHITESPACE, 'using:inline'],
            ],

            // comments
            [
                '^ *\.\..*(\n( +.*\n|\n)+)?',
                self::TYPE_COMMENT,
            ],

            // field list
            [
                '^( *)(:(?:\\\\|\\:|[^:\n])+:(?=\s))([ \t\n]*)',
                [self::TYPE_WHITESPACE, self::TYPE_FIELDLIST, self::TYPE_WHITESPACE],
            ],

            // definition list
            [
                '^(\S.*(?<!::)\n)((?:(?: +.*)\n)+)',
                ['using:inline', 'using:inline'],
            ],

            // code blocks
            [
                '^(::)(\n[ \t]*\n)([ \t]+)(.*)(\n)((?:(?:\3.*|)\n)+)',
                self::TYPE_CODE_BLOCK,
            ],
        ],

        'inline' => [
            ['\\\\\.', self::TYPE_TEXT],   // escape
            ['``', self::TYPE_LITERAL, 'literal'], // code

            // reference
            ['`.+?<.+?>`__?', self::TYPE_REFERENCE],
            ['`.+?`__?', self::TYPE_REFERENCE],

            // role
            ['(`.+?`)(:[a-zA-Z0-9:-]+?:)?', self::TYPE_ROLE],
            ['(:[a-zA-Z0-9:-]+?:)(`.+?`)', self::TYPE_ROLE],

            ['\*\*.+?\*\*', self::TYPE_STRONG],
            ['\*.+?\*', self::TYPE_EM],
            ['\[.*?\]_', self::TYPE_FOOTNOTE],
            ['<.+?>', self::TYPE_HYPERLINK],
            ['[^\n\\\[*`:]+', self::TYPE_TEXT],
            ['.', self::TYPE_TEXT],
        ],

        'literal' => [
            ['[^`]+', self::TYPE_LITERAL],
            ['``', self::TYPE_LITERAL, '#pop'],
            ['`', self::TYPE_LITERAL],
        ],
    ];

    public function __construct()
    {
        if (null === self::$tokens) {
            self::$_tokens['document'] = [...self::$_tokens['document'], ...self::$_tokens['inline']];
        }
    }

    public function tokenize(string $text): TokenStream
    {
        self::$tokens = $this->processTokens(self::$_tokens);
        $this->doTokenize($text);

        $tokenStream = new TokenStream($this->ast);
        $this->ast = [];

        return $tokenStream;
    }

    private function processTokens(array $tokens): array
    {
        return array_map(function ($tokensByState) {
            return array_map(function ($token) {
                    switch (count($token)) {
                        case 2:
                            [$regex, $typesByGroup] = $token;
                            $newState = null;

                            break;
                        case 3:
                            [$regex, $typesByGroup, $newState] = $token;

                            break;
                        default:
                            throw new \LogicException("Invalid number of items set for token:\n".print_r($token, true));
                    }

                    return [
                        $regex,
                        function (array $matches, string $currentState) use ($typesByGroup, $newState) {
                            $ast = [];

                            // if one type is provided, the whole match is of that type
                            if (!is_array($typesByGroup)) {
                                $ast[] = [$typesByGroup, $matches[0][0]];
                            } else {
                                // otherwise, process each match group
                                foreach ($typesByGroup as $i => $typeByGroup) {
                                    if (!isset($matches[$i + 1])) {
                                        throw new \LogicException(
                                            sprintf(
                                                "Expected %d match groups, only got %d.",
                                                count($typesByGroup),
                                                count($matches) - 1
                                            )
                                        );
                                    }

                                    $ast[] = [$typeByGroup, $matches[$i + 1][0]];
                                }
                            }

                            foreach ($ast as $match) {
                                if (is_string($match[0]) && 'using:' === substr($match[0], 0, 6)) {
                                    $this->doTokenize($match[1], substr($match[0], 6));

                                    continue;
                                }

                                $this->ast[] = $match;
                            }

                            return $newState ?? $currentState;
                        }
                    ];
                }, $tokensByState);
        }, $tokens);
    }

    private function doTokenize(string $text, $state = 'document'): void
    {
        $pos = 0;
        $stateHistory = [$state];
        $textLength = strlen($text);
        while (true) {
            $_pos = $pos;

            foreach (self::$tokens[$state] as $token) {
                [$pattern, $action] = $token;

                $regex = '/'.$pattern.'/m';
                if (!preg_match($regex, $text, $matches, \PREG_OFFSET_CAPTURE, $pos)) {
                    continue;
                }

                if ($matches[0][1] !== $pos) {
                    continue;
                }

                // this token is a match
                $newState = $action($matches, $state);

                $pos += strlen($matches[0][0]);

                if ($newState !== $state) {
                    if ('#pop' === $newState) {
                        array_pop($stateHistory);
                        $state = end($stateHistory);
                    } else {
                        $state = $newState;
                        $stateHistory[] = $state;
                    }
                }

                break;
            }

            if ($pos === $textLength) {
                return;
            }

            if ("\n" === $text[$pos]) {
                $this->ast[] = [self::TYPE_WHITESPACE, $text[$pos]];
                ++$pos;
                continue;
            }

            if ($_pos === $pos) {
                throw new \LogicException('No match for: '.substr($text, $pos, 100));
            }
        }
    }
}
