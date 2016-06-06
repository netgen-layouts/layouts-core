<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

class ItemViewType
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
     * Constructor.
     *
     * @param string $identifier
     * @param string $name
     */
    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

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
