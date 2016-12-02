<?php

namespace Netgen\BlockManager\View\View\BlockView;

use Netgen\BlockManager\API\Values\Page\Block as APIBlock;

class Block implements APIBlock
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block
     */
    protected $innerBlock;

    /**
     * @var array
     */
    protected $dynamicParameters;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $innerBlock
     * @param array $dynamicParameters
     */
    public function __construct(APIBlock $innerBlock, $dynamicParameters = array())
    {
        $this->innerBlock = $innerBlock;
        $this->dynamicParameters = $dynamicParameters;
    }

    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->innerBlock->getId();
    }

    /**
     * Returns layout ID to which this block belongs.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->innerBlock->getLayoutId();
    }

    /**
     * Returns zone identifier to which this block belongs.
     *
     * @return string
     */
    public function getZoneIdentifier()
    {
        return $this->innerBlock->getZoneIdentifier();
    }

    /**
     * Returns the position of this block in the zone.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->innerBlock->getPosition();
    }

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getBlockDefinition()
    {
        return $this->innerBlock->getBlockDefinition();
    }

    /**
     * Returns if the block is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->innerBlock->isPublished();
    }

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->innerBlock->getViewType();
    }

    /**
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType()
    {
        return $this->innerBlock->getItemViewType();
    }

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName()
    {
        return $this->innerBlock->getName();
    }

    /**
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->innerBlock->getStatus();
    }

    /**
     * Returns all parameter values.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    public function getParameters()
    {
        return $this->innerBlock->getParameters();
    }

    /**
     * Returns the specified parameter value.
     *
     * @param string $parameter
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameter($parameter)
    {
        return $this->innerBlock->getParameter($parameter);
    }

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter)
    {
        return $this->innerBlock->hasParameter($parameter);
    }

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getDynamicParameter($parameter)
    {
        if (!isset($this->dynamicParameters[$parameter])) {
            return null;
        }

        if (!is_callable($this->dynamicParameters[$parameter])) {
            return $this->dynamicParameters[$parameter];
        }

        return $this->dynamicParameters[$parameter]();
    }

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasDynamicParameter($parameter)
    {
        return array_key_exists($parameter, $this->dynamicParameters);
    }
}
