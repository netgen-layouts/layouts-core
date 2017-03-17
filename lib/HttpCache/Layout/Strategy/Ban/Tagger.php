<?php

namespace Netgen\BlockManager\HttpCache\Layout\Strategy\Ban;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\Layout\TaggerInterface;
use Symfony\Component\HttpFoundation\Response;

class Tagger implements TaggerInterface
{
    /**
     * Tags the response with data from the provided layout.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function tag(Response $response, Layout $layout)
    {
        $response->headers->set('X-Layout-Id', $layout->getId());
    }
}
