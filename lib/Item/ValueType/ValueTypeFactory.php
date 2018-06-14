<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\ValueType;

final class ValueTypeFactory
{
    /**
     * Builds the value type.
     */
    public static function buildValueType(string $identifier, array $config): ValueType
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
