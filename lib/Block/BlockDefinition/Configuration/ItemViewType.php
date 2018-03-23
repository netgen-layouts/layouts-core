<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Value;

final class ItemViewType extends Value
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
     * Returns the item view type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the item view type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
