<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Block as BlockInterface;
use Netgen\BlockManager\API\Values\Value;

class Block extends Value implements BlockInterface
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $zoneId;

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
     * Returns zone ID to which this block belongs.
     *
     * @return int|string
     */
    public function getZoneId()
    {
        return $this->zoneId;
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
}
