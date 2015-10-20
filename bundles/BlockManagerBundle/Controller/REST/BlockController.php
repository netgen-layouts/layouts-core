<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\REST;

use Netgen\BlockManager\API\Values\Page\Block;

class BlockController extends Controller
{
    /**
     * Serializes the block object.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getBlock(Block $block)
    {
        $blockView = $this->buildViewObject($block, array(), 'manager');

        return $this->serializeObject($blockView);
    }
}
