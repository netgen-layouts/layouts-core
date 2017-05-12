<?php

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\ValueObject;

class Zone extends ValueObject
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

        return in_array($blockDefinition, $this->allowedBlockDefinitions, true);
    }
}