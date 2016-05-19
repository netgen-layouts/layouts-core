<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\BlockManager\Value as BaseValue;

abstract class AbstractValue extends BaseValue
{
    /**
     * Construct object optionally with a set of properties.
     *
     * Read only properties values must be set using $properties as they are not writable anymore
     * after object has been created.
     *
     * @param array $properties
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If one of the properties does not exist in value object
     */
    public function __construct(array $properties = array())
    {
        try {
            parent::__construct($properties);
        } catch (BaseInvalidArgumentException $e) {
            throw new InvalidArgumentException('properties', $e->getMessage());
        }
    }
}
