<?php

namespace App\Command;

use App\Rst\Tokenizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TokenizerCommand extends Command
{
    public function __construct()
    {
        parent::__construct('tokenize');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rst = file_get_contents(getcwd().'/form/bootstrap4.rst');

        $tokenizer  = new Tokenizer();
        $tokens = $tokenizer->tokenize($rst);

        echo (string) $tokens;

        return 0;
    }
}
