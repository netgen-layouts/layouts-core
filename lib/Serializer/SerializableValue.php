<?php

namespace Netgen\BlockManager\Serializer;

use Netgen\BlockManager\API\Values\Value;

class SerializableValue
{
    /**
     * @var \Netgen\BlockManager\API\Values\Value
     */
    protected $value;

    /**
     * @var int
     */
    protected $version;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param int $version
     */
    public function __construct(Value $value, $version)
    {
        $this->value = $value;
        $this->version = $version;
    }

    /**
     * Returns the value
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the API version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
