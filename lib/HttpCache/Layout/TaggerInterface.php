<?php

namespace Netgen\BlockManager\HttpCache\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Symfony\Component\HttpFoundation\Response;

interface TaggerInterface
{
    /**
     * Tags the response with data from the provided layout.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function tag(Response $response, Layout $layout);
}
