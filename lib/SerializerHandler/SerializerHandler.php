<?php

namespace Netgen\BlockManager\SerializerHandler;

use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

abstract class SerializerHandler
{
    /**
     * Serializes the value.
     *
     * @param \JMS\Serializer\JsonSerializationVisitor $visitor
     * @param mixed $value
     * @param array $type
     * @param \JMS\Serializer\SerializationContext $context
     *
     * @return array|\ArrayObject
     */
    public function serialize(JsonSerializationVisitor $visitor, $value, array $type, SerializationContext $context)
    {
        return $visitor->visitArray(
            $this->getValueData($value),
            $type,
            $context
        );
    }

    /**
     * Returns the data that will be serialized.
     *
     * @param mixed $value
     *
     * @return array
     */
    abstract public function getValueData($value);
}
