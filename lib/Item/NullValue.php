<?php

namespace Netgen\BlockManager\Item;

class NullValue
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $valueType;

    /**
     * Constructor.
     *
     * @param int|string $id
     * @param string $valueType
     */
    public function __construct($id, $valueType)
    {
        $this->id = $id;
        $this->valueType = $valueType;
    }

    /**
     * Returns the value ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value type.
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }
}
