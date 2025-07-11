<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\TokenParser;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone as RenderZoneNode;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

use function sprintf;

final class RenderZone extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $expressionParser = Environment::VERSION_ID >= 32100 ?
            $this->parser :
            $this->parser->getExpressionParser();

        $stream = $this->parser->getStream();

        $context = null;
        $zone = $expressionParser->parseExpression();

        while (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test(Token::NAME_TYPE, 'context')) {
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $context = $expressionParser->parseExpression();

                continue;
            }

            $token = $stream->getCurrent();

            throw new SyntaxError(
                sprintf(
                    'Unexpected token "%s" of value "%s".',
                    Environment::VERSION_ID >= 31900 ?
                        $token->toEnglish() :
                        Token::typeToEnglish($token->getType()),
                    $token->getValue(),
                ),
                $token->getLine(),
                $stream->getSourceContext(),
            );
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new RenderZoneNode($zone, $context, $token->getLine());
    }

    public function getTag(): string
    {
        return 'nglayouts_render_zone';
    }
}
