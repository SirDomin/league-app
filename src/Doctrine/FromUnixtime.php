<?php

namespace App\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class FromUnixtime extends FunctionNode
{
    public $timestamp;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'TO_TIMESTAMP(' . $this->timestamp->dispatch($sqlWalker) . ')';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER); // Match the function name
        $parser->match(Lexer::T_OPEN_PARENTHESIS); // Match the '('
        $this->timestamp = $parser->ArithmeticPrimary(); // Parse the function argument
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); // Match the ')'
    }
}
