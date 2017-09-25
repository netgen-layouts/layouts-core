<?php

namespace Netgen\BlockManager\HttpCache;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Symfony\Component\HttpFoundation\Response;

final class Tagger implements TaggerInterface
{
    public function tagLayout(Response $response, Layout $layout)
    {
        $response->headers->set('X-Layout-Id', $layout->getId());
    }

    public function tagBlock(Response $response, Block $block)
    {
        $response->headers->set('X-Block-Id', $block->getId());
        $response->headers->set('X-Origin-Layout-Id', $block->getLayoutId());
    }
}
