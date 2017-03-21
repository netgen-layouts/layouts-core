<?php

namespace Netgen\BlockManager\View\View\BlockView;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;

class Block implements APIBlock
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block
     */
    protected $innerBlock;

    /**
     * @var array
     */
    protected $dynamicParameters;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $innerBlock
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
     * Returns the ID of the layout where the block is located.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->innerBlock->getLayoutId();
    }

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->innerBlock->getDefinition();
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
     * Returns all placeholders from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    public function getPlaceholders()
    {
        return $this->innerBlock->getPlaceholders();
    }

    /**
     * Returns the specified placeholder or null if placeholder does not exist.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder($identifier)
    {
        return $this->innerBlock->getPlaceholder($identifier);
    }

    /**
     * Returns if blocks has a specified placeholder.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPlaceholder($identifier)
    {
        return $this->innerBlock->hasPlaceholder($identifier);
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
        if (!$this->hasDynamicParameter($parameter)) {
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
