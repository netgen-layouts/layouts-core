<?php

namespace Netgen\BlockManager\HttpCache\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Symfony\Component\HttpFoundation\Response;

interface TaggerInterface
{
    /**
     * Tags the response with data from the provided block.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function tag(Response $response, Block $block);
}
