<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

enum UrlType: string
{
    case Default = 'default';

    case Admin = 'admin';
}
