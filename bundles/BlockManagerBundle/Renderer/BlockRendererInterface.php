<?php

namespace Netgen\Bundle\BlockManagerBundle\Renderer;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\ViewInterface;

interface BlockRendererInterface
{
    /**
     * Renders the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderBlock(Block $block, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array());

    /**
     * Renders the block via ESI fragment.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderBlockFragment(Block $block, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array());
}
