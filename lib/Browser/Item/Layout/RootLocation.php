<?php

declare(strict_types=1);

namespace Netgen\Layouts\Browser\Item\Layout;

use Netgen\ContentBrowser\Item\LocationInterface;

final class RootLocation implements LocationInterface
{
    public function getLocationId(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'All layouts';
    }

    public function getParentId(): ?int
    {
        return null;
    }
}
