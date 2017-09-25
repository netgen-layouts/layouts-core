<?php

namespace Netgen\BlockManager\Item\ValueType;

use Netgen\BlockManager\ValueObject;

/**
 * Value type represents a model of a type of CMS value available in Netgen Layouts.
 *
 * A value type is defined in configuration and specifies the identifier of the value
 * which is used, together with the ID of the value, to reference a single instance
 * in Netgen Layouts.
 *
 * @final
 */
class ValueType extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * Returns the value type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns if the value type is enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Returns the value type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
