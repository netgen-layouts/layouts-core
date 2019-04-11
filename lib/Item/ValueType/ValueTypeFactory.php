<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item\ValueType;

final class ValueTypeFactory
{
    /**
     * Builds the value type.
     */
    public static function buildValueType(string $identifier, array $config): ValueType
    {
        return ValueType::fromArray(
            [
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
            ]
        );
    }
}
