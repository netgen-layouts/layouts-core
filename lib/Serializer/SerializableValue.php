<?php

namespace Netgen\BlockManager\Serializer;

class SerializableValue
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @var int
     */
    public $version;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param int $version
     */
    public function __construct($value, $version)
    {
        $this->value = $value;
        $this->version = $version;
    }
}
