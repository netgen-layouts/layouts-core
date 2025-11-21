<?php

declare(strict_types=1);

namespace Netgen\Layouts\Browser\Item\Layout;

use Netgen\ContentBrowser\Item\LocationInterface;

final class RootLocation implements LocationInterface
{
    public string $locationId {
        get => '';
    }

    public string $name {
        get => 'All layouts';
    }

    public null $parentId {
        get => null;
    }
}
