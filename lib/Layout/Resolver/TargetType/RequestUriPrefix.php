<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

final class RequestUriPrefix extends RequestUri
{
    public static function getType(): string
    {
        return 'request_uri_prefix';
    }
}
