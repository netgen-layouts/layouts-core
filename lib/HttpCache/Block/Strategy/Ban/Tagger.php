<?php

namespace Netgen\BlockManager\HttpCache\Block\Strategy\Ban;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\TaggerInterface;
use Symfony\Component\HttpFoundation\Response;

class Tagger implements TaggerInterface
{
    /**
     * Tags the response with data from the provided block.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function tag(Response $response, Block $block)
    {
        $response->headers->set('X-Block-Id', $block->getId());
        $response->headers->set('X-Origin-Layout-Id', $block->getLayoutId());
    }
}
