<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Value;

enum LinkType: string
{
    case Url = 'url';
    case RelativeUrl = 'relative_url';
    case Email = 'email';
    case Phone = 'phone';
    case Internal = 'internal';
}
