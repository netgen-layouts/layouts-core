<?php

namespace Netgen\BlockManager\HttpCache;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Symfony\Component\HttpFoundation\Response;

class Tagger implements TaggerInterface
{
    /**
     * Tags the response with data from the provided layout.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function tagLayout(Response $response, Layout $layout)
    {
        $response->headers->set('X-Layout-Id', $layout->getId());
    }

    /**
     * Tags the response with data from the provided block.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function tagBlock(Response $response, Block $block)
    {
        $response->headers->set('X-Block-Id', $block->getId());
        $response->headers->set('X-Origin-Layout-Id', $block->getLayoutId());
    }
}
