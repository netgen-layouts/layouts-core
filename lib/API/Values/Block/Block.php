<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;

interface Block extends Value, ParameterBasedValue, ConfigAwareValue
{
    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the ID of the layout where the block is located.
     *
     * @return int|string
     */
    public function getLayoutId();

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition();

    /**
     * Returns if the block is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType();

    /**
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType();

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns all placeholders from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    public function getPlaceholders();

    /**
     * Returns the specified placeholder.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the placeholder does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder($identifier);

    /**
     * Returns if blocks has a specified placeholder.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPlaceholder($identifier);

    /**
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getDynamicParameter($parameter);

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasDynamicParameter($parameter);
}
