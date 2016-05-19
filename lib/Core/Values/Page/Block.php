<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\AbstractValue;

class Block extends AbstractValue implements APIBlock
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var string
     */
    protected $zoneIdentifier;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $definitionIdentifier;

    /**
     * @var string
     */
    protected $viewType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns layout ID to which this block belongs.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns zone identifier to which this block belongs.
     *
     * @return string
     */
    public function getZoneIdentifier()
    {
        return $this->zoneIdentifier;
    }

    /**
     * Returns the position of this block in the zone.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getDefinitionIdentifier()
    {
        return $this->definitionIdentifier;
    }

    /**
     * Returns block parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns specified block parameter.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : null;
    }

    /**
     * Returns if block has a specified parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter)
    {
        return isset($this->parameters[$parameter]);
    }

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
