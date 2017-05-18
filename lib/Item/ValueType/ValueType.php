<?php

namespace Netgen\BlockManager\Item\ValueType;

use Netgen\BlockManager\ValueObject;

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
