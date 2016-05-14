<?php

namespace Netgen\BlockManager\Configuration\LayoutType;

class Zone
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
     * @var array
     */
    protected $allowedBlockTypes = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $name
     * @param array $allowedBlockTypes
     */
    public function __construct($identifier, $name, array $allowedBlockTypes)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->allowedBlockTypes = $allowedBlockTypes;
    }

    /**
     * Returns the zone identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the zone name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns allowed block type identifiers.
     *
     * @return array
     */
    public function getAllowedBlockTypes()
    {
        return $this->allowedBlockTypes;
    }
}
