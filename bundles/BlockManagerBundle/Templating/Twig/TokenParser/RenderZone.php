<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone as RenderZoneNode;
use Netgen\BlockManager\View\ViewInterface;
use Twig_TokenParser;
use Twig_Error_Syntax;
use Twig_Token;

class RenderZone extends Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token
     *
     * @throws \Twig_Error_Syntax
     *
     * @return \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone
     */
    public function parse(Twig_Token $token)
    {
        $stream = $this->parser->getStream();

        $context = ViewInterface::CONTEXT_DEFAULT;
        $zone = $this->parser->getExpressionParser()->parseExpression();

        while (!$stream->test(Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(Twig_Token::NAME_TYPE, 'context')) {
                $stream->next();
                $stream->expect(Twig_Token::OPERATOR_TYPE, '=');
                $context = $stream->expect(Twig_Token::STRING_TYPE)->getValue();
            } else {
                $token = $stream->getCurrent();
                throw new Twig_Error_Syntax(
                    sprintf(
                        'Unexpected token "%s" of value "%s".',
                        Twig_Token::typeToEnglish($token->getType()),
                        $token->getValue()
                    ),
                    $token->getLine(),
                    $stream->getFilename()
                );
            }
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

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
