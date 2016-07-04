<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

class RequestUriPrefix extends RequestUri
{
    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType()
    {
        return 'request_uri_prefix';
    }
}
