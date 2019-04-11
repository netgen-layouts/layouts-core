<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetType;

final class RequestUriPrefix extends RequestUri
{
    public static function getType(): string
    {
        return 'request_uri_prefix';
    }
}
