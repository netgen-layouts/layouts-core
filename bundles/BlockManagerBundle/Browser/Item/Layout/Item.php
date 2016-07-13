<?php

namespace Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout;

use Netgen\BlockManager\API\Values\Page\LayoutInfo;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

class Item implements ItemInterface, LayoutInterface
{
    const TYPE = 'ngbm_layout';

    /**
     * @var \Netgen\BlockManager\API\Values\Page\LayoutInfo
     */
    protected $layout;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutInfo $layout
     */
    public function __construct(LayoutInfo $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Returns the type.
     *
     * @return int|string
     */
    public function getType()
    {
        return self::TYPE;
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
        return;
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
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutInfo
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
