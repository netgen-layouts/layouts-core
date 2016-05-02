<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Core\Values\Page\Block;

class BlockMapper extends Mapper
{
    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function mapBlock(PersistenceBlock $block)
    {
        return new Block(
            array(
                'id' => $block->id,
                'layoutId' => $block->layoutId,
                'zoneIdentifier' => $block->zoneIdentifier,
                'position' => $block->position,
                'definitionIdentifier' => $block->definitionIdentifier,
                'parameters' => $block->parameters,
                'viewType' => $block->viewType,
                'name' => $block->name,
                'status' => $block->status,
            )
        );
    }
}
