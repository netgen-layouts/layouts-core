<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item\ValueType;

use Netgen\Layouts\Utils\HydratorTrait;

/**
 * Value type represents a model of a type of CMS value available in Netgen Layouts.
 *
 * A value type is defined in configuration and specifies the identifier of the value
 * which is used, together with the ID of the value, to reference a single instance
 * in Netgen Layouts.
 */
final class ValueType
{
    use HydratorTrait;

    /**
     * Returns the value type identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns if the value type is enabled or not.
     */
    public private(set) bool $isEnabled;

    /**
     * Returns the value type name.
     */
    public private(set) string $name;

    /**
     * Returns if the value type supports selecting manual items through Content Browser.
     */
    public private(set) bool $supportsManualItems;
}
