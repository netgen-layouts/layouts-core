<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder;

class RequestUriPrefix extends RequestUri
{
    /**
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'request_uri_prefix';
    }
}
