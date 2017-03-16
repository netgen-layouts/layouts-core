<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;

class TwigBlockDefinition extends BlockDefinition implements TwigBlockDefinitionInterface
{
    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $viewTypes
     * @param \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface $handler
     */
    public function __construct($identifier, array $viewTypes = array(), TwigBlockDefinitionHandlerInterface $handler = null)
    {
        parent::__construct($identifier, $viewTypes, $handler ?: new TwigBlockDefinitionHandler());
    }

    /**
     * Returns the name of the Twig block to use.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block)
    {
        return $this->handler->getTwigBlockName($block);
    }
}
