<?php

namespace App\Rst;

class TokenStream extends \ArrayIterator
{
    public function flatten($ignoreWhitespace = true): self
    {
        $tokens = [];
        $_currentToken = null;
        foreach ($this as $token) {
            if (null === $_currentToken) {
                $_currentToken = $token;

                continue;
            }

            if (!($ignoreWhitespace && Tokenizer::TYPE_WHITESPACE === $token[0]) && $token[0] !== $_currentToken[0]) {
                $tokens[] = $_currentToken;
                $_currentToken = $token;

                continue;
            }

            $_currentToken[1] .= $token[1];
        }

        $tokens[] = $_currentToken;

        return new static($tokens);
    }

    public function __toString()
    {
        $s = '';
        foreach ($this->flatten() as $token) {
            $s .= sprintf("%23s  %s\n", Tokenizer::$typeMap[$token[0]], str_replace("\n", "\n                         ", $token[1]));
        }

        return $s;
    }
}
