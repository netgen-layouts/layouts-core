<?php

namespace Netgen\BlockManager\Item\ValueType;

class ValueTypeFactory
{
    /**
     * Builds the value type.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return \Netgen\BlockManager\Item\ValueType\ValueType
     */
    public static function buildValueType($identifier, array $config)
    {
        return new ValueType(
            array(
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
            )
        );
    }
}
