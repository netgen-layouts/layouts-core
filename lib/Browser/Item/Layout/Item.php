<?php

namespace Netgen\BlockManager\Browser\Item\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\ContentBrowser\Item\ItemInterface;

final class Item implements ItemInterface, LayoutInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getValue()
    {
        return $this->layout->getId();
    }

    public function getName()
    {
        return $this->layout->getName();
    }

    public function isVisible()
    {
        return true;
    }

    public function isSelectable()
    {
        return true;
    }

    public function getLayout()
    {
        return $this->layout;
    }
}
