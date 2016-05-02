<?php

namespace Netgen\BlockManager\API\Service\Mapper;

use Netgen\BlockManager\Persistence\Values\Page\Block;

interface BlockMapper
{
    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function mapBlock(Block $block);
}
