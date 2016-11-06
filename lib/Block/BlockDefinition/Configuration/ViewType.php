<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\ValueObject;

class ViewType extends ValueObject
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
     * @var array
     */
    protected $validParameters;

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
     * Returns the item view type identifiers.
     *
     * @return string[]
     */
    public function getItemViewTypeIdentifiers()
    {
        return array_keys($this->itemViewTypes);
    }

    /**
     * Returns the valid parameters.
     *
     * @return array
     */
    public function getValidParameters()
    {
        return $this->validParameters;
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If item view type does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType
     */
    public function getItemViewType($viewTypeIdentifier)
    {
        if (!$this->hasItemViewType($viewTypeIdentifier)) {
            throw new InvalidArgumentException(
                'viewTypeIdentifier',
                sprintf(
                    'Item view type "%s" does not exist in "%s" view type.',
                    $viewTypeIdentifier,
                    $this->identifier
                )
            );
        }

        return $this->itemViewTypes[$viewTypeIdentifier];
    }
}
