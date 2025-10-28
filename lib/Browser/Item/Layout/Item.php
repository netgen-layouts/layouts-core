<?php

declare(strict_types=1);

namespace Netgen\Layouts\Browser\Item\Layout;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\Layouts\API\Values\Layout\Layout;

final class Item implements ItemInterface, LayoutInterface
{
    public function __construct(
        private Layout $layout,
    ) {}

    public function getValue(): string
    {
        return $this->layout->getId()->toString();
    }

    public function getName(): string
    {
        return $this->layout->getName();
    }

    public function isVisible(): true
    {
        return true;
    }

    public function isSelectable(): true
    {
        return true;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }
}
