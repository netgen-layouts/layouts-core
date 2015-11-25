<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;

abstract class Value
{
    /**
     * Construct object optionally with a set of properties.
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If one of the properties does not exist in value object
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new InvalidArgumentException(
                    'properties',
                    'Property "' . $property . '" does not exist in "' . get_class($this) . '" class.'
                );
            }

            $this->$property = $value;
        }
    }
}
