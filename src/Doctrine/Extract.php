<?php

namespace App\Doctrine;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

class Extract extends FunctionNode
{
    public $field = null;
    public $source = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER); // Match the function name
        $parser->match(Lexer::T_OPEN_PARENTHESIS); // Match the '('

        // Handle the field, expecting a string like 'DOW'
        $this->field = $parser->Literal();

        $parser->match(Lexer::T_FROM); // Match 'FROM' keyword
        $this->source = $parser->ArithmeticPrimary(); // Parse the source expression
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); // Match the ')'
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'EXTRACT(' . $this->field->value . ' FROM ' . $this->source->dispatch($sqlWalker) . ')';

    }
}
