<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone as RenderZoneNode;
use Twig\Error\SyntaxError;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class RenderZone extends AbstractTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param \Twig\Token $token
     *
     * @throws \Twig\Error\SyntaxError
     *
     * @return \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone
     */
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        $context = null;
        $zone = $this->parser->getExpressionParser()->parseExpression();

        while (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test(Token::NAME_TYPE, 'context')) {
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $context = $this->parser->getExpressionParser()->parseExpression();

                continue;
            }

            $token = $stream->getCurrent();
            throw new SyntaxError(
                sprintf(
                    'Unexpected token "%s" of value "%s".',
                    Token::typeToEnglish($token->getType()),
                    $token->getValue()
                ),
                $token->getLine(),
                $stream->getSourceContext()->getName()
            );
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new RenderZoneNode($zone, $context, $token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string
     */
    public function getTag()
    {
        return 'ngbm_render_zone';
    }
}
