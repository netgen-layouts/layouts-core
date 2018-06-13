<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

final class RequestUriPrefix extends RequestUri
{
    public function getType()
    {
        return 'request_uri_prefix';
    }
}
