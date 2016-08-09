<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\LayoutInfo as APILayoutInfo;
use Netgen\BlockManager\ValueObject;

class LayoutInfo extends ValueObject implements APILayoutInfo
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    protected $layoutType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $modified;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var bool
     */
    protected $shared;

    /**
     * Returns the layout ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the layout type.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public function getLayoutType()
    {
        return $this->layoutType;
    }

    /**
     * Returns the layout human readable name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns when was the layout created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Returns when was the layout last updated.
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Returns the status of the layout.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns if the layout is shared.
     *
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }
}
