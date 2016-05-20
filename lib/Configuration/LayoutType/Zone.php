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
    protected $allowedBlockDefinitions = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $name
     * @param array $allowedBlockDefinitions
     */
    public function __construct($identifier, $name, array $allowedBlockDefinitions)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->allowedBlockDefinitions = $allowedBlockDefinitions;
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
     * Returns allowed block definition identifiers.
     *
     * @return array
     */
    public function getAllowedBlockDefinitions()
    {
        return $this->allowedBlockDefinitions;
    }

    /**
     * Returns if block definition is allowed within the zone.
     *
     * @param string $blockDefinition
     *
     * @return bool
     */
    public function isBlockDefinitionAllowed($blockDefinition)
    {
        if (empty($this->allowedBlockDefinitions)) {
            return true;
        }

        return in_array($blockDefinition, $this->allowedBlockDefinitions);
    }
}
