<?php

namespace Netgen\BlockManager\Persistence\Values;

use InvalidArgumentException;

abstract class Value
{
    /**
     * Construct object optionally with a set of properties.
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new InvalidArgumentException('Property "' . $property . '" does not exist in "' . get_class($this) . '" class.');
            }

            $this->$property = $value;
        }
    }
}
