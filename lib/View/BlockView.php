<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

class BlockView extends View implements BlockViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     */
    public function __construct(Block $block, BlockDefinitionInterface $blockDefinition)
    {
        $this->valueObject = $block;

        $this->internalParameters['block'] = $block;
        $this->internalParameters['block_definition'] = $blockDefinition;
    }

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock()
    {
        return $this->valueObject;
    }

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getBlockDefinition()
    {
        return $this->internalParameters['block_definition'];
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'block_view';
    }
}
