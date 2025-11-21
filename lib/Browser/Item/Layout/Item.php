<?php

declare(strict_types=1);

namespace Netgen\Layouts\Browser\Item\Layout;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\Layouts\API\Values\Layout\Layout;

final class Item implements ItemInterface, LayoutInterface
{
    public string $value {
        get => $this->layout->getId()->toString();
    }

    public string $name {
        get => $this->layout->getName();
    }

    public true $isVisible {
        get => true;
    }

    public true $isSelectable {
        get => true;
    }

    public function __construct(
        private(set) Layout $layout,
    ) {}
}
