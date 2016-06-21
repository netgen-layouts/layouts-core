<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

class RequestUriPrefix extends RequestUri
{
    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'request_uri_prefix';
    }
}
