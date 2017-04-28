<?php

namespace Netgen\BlockManager\Item\Registry;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueType\ValueType;

class ValueTypeRegistry implements ValueTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType[]
     */
    protected $valueTypes = array();

    /**
     * Adds a value type to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Item\ValueType\ValueType $valueType
     */
    public function addValueType($identifier, ValueType $valueType)
    {
        $this->valueTypes[$identifier] = $valueType;
    }

    /**
     * Returns if registry has a value type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasValueType($identifier)
    {
        return isset($this->valueTypes[$identifier]);
    }

    /**
     * Returns a value type for provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If value type does not exist
     *
     * @return \Netgen\BlockManager\Item\ValueType\ValueType
     */
    public function getValueType($identifier)
    {
        if (!$this->hasValueType($identifier)) {
            throw ItemException::noValueType($identifier);
        }

        return $this->valueTypes[$identifier];
    }

    /**
     * Returns all value types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Item\ValueType\ValueType[]
     */
    public function getValueTypes($onlyEnabled = false)
    {
        if (!$onlyEnabled) {
            return $this->valueTypes;
        }

        return array_filter(
            $this->valueTypes,
            function (ValueType $valueType) {
                return $valueType->isEnabled();
            }
        );
    }
}
