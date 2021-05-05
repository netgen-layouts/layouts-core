<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item\ValueType;

final class ValueTypeFactory
{
    /**
     * Builds the value type.
     *
     * @param array<string, mixed> $config
     */
    public static function buildValueType(string $identifier, array $config): ValueType
    {
        return ValueType::fromArray(
            [
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
                'supportsManualItems' => $config['manual_items'],
            ],
        );
    }
}
