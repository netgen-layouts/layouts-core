<?php

namespace Netgen\BlockManager\Block\BlockType;

use Netgen\BlockManager\Value;

/**
 * Block type represents the starting configuration of the block. E.g. while
 * block definition specifies what view types the block can have, block type
 * specifies the exact view type the new block will have once created. The
 * same goes for item view types and all of the block parameters.
 *
 * @final
 */
class BlockType extends Value
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $definition;

    /**
     * @var array
     */
    protected $defaults = array();

    /**
     * Returns the block type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns if the block type is enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Returns the block type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the block type icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Returns the default block values.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns the default block name.
     *
     * @return string
     */
    public function getDefaultName()
    {
        return isset($this->defaults['name']) ? $this->defaults['name'] : '';
    }

    /**
     * Returns the default block view type.
     *
     * @return string
     */
    public function getDefaultViewType()
    {
        return isset($this->defaults['view_type']) ? $this->defaults['view_type'] : '';
    }

    /**
     * Returns the default block item view type.
     *
     * @return string
     */
    public function getDefaultItemViewType()
    {
        return isset($this->defaults['item_view_type']) ? $this->defaults['item_view_type'] : '';
    }

    /**
     * Returns the default block parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return isset($this->defaults['parameters']) ? $this->defaults['parameters'] : array();
    }
}
