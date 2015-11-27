<?php

namespace Netgen\BlockManager\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target;

class RequestUriPrefix extends Target
{
    /**
     * Returns the unique identifier of the target
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'request_uri_prefix';
    }
}
