<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Values\View as SerializedView;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;

final class Load extends Controller
{
    /**
     * Loads a block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function __invoke(Block $block)
    {
        return new SerializedView($block, Version::API_V1);
    }
}
