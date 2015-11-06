<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Exceptions\InvalidArgumentException;

abstract class Value
{
    /**
     * Construct object optionally with a set of properties.
     *
     * Read only properties values must be set using $properties as they are not writable anymore
     * after object has been created.
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If one of the properties does not exist in value object
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new InvalidArgumentException(
                    'properties',
                    $property,
                    'Property "' . $property . '" does not exist in "' . get_class($this) . '" class.'
                );
            }

            $this->$property = $value;
        }
    }
}
