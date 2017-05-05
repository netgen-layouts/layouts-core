<?php

namespace Netgen\BlockManager\Browser\Item\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\ContentBrowser\Item\ItemInterface;

class Item implements ItemInterface, LayoutInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    protected $layout;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->layout->getId();
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->layout->getName();
    }

    /**
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return null;
    }

    /**
     * Returns if the item is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return true;
    }

    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
