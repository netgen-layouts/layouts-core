<?php

namespace Netgen\BlockManager\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target;

class RequestUriPrefix extends Target
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'request_uri_prefix';
    }
}
