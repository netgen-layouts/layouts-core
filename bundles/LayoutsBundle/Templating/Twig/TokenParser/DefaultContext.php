<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\TokenParser;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext as DefaultContextNode;
use Twig\Environment;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class DefaultContext extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $expressionParser = Environment::VERSION_ID >= 32100 ?
            $this->parser :
            $this->parser->getExpressionParser();

        $expression = $expressionParser->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new DefaultContextNode($expression, $token->getLine());
    }

    public function getTag(): string
    {
        return 'nglayouts_default_context';
    }
}
