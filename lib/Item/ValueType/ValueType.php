<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\ValueType;

use Netgen\BlockManager\Value;

/**
 * Value type represents a model of a type of CMS value available in Netgen Layouts.
 *
 * A value type is defined in configuration and specifies the identifier of the value
 * which is used, together with the ID of the value, to reference a single instance
 * in Netgen Layouts.
 *
 * @final
 */
class ValueType extends Value
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var string
     */
    private $name;

    /**
     * Returns the value type identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns if the value type is enabled or not.
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Returns the value type name.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
