<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use RuntimeException;

class ViewType
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType[]
     */
    protected $itemViewTypes = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $name
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType[] $itemViewTypes
     */
    public function __construct($identifier, $name, array $itemViewTypes = array())
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->itemViewTypes = $itemViewTypes;
    }

    /**
     * Returns the view type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the view type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the item view types.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType[]
     */
    public function getItemViewTypes()
    {
        return $this->itemViewTypes;
    }

    /**
     * Returns if the view type has an item view type with provided identifier.
     *
     * @param string $viewTypeIdentifier
     *
     * @return bool
     */
    public function hasItemViewType($viewTypeIdentifier)
    {
        return isset($this->itemViewTypes[$viewTypeIdentifier]);
    }

    /**
     * Returns the item view type with provided identifier.
     *
     * @param string $viewTypeIdentifier
     *
     * @throws \RuntimeException If item view type does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType
     */
    public function getItemViewType($viewTypeIdentifier)
    {
        if (!$this->hasItemViewType($viewTypeIdentifier)) {
            throw new RuntimeException(
                sprintf(
                    "Item view type '%s' does not exist in '%s' view type.",
                    $viewTypeIdentifier,
                    $this->identifier
                )
            );
        }

        return $this->itemViewTypes[$viewTypeIdentifier];
    }
}
