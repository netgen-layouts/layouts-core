<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;

interface Block extends Value, ParameterBasedValue
{
    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId();

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
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus();
}
