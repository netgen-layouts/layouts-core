<?php

namespace Netgen\BlockManager\Item\ValueType;

final class ValueTypeFactory
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
            [
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
            ]
        );
    }
}
