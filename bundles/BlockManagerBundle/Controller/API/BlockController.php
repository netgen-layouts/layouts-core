<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
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
