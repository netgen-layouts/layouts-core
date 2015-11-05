<?php

namespace Netgen\BlockManager\Serializer;

use JMS\Serializer\VisitorInterface;
use JMS\Serializer\SerializationContext;

abstract class Serializer
{
    /**
     * Serializes the value.
     *
     * @param \JMS\Serializer\VisitorInterface $visitor
     * @param mixed $value
     * @param array $type
     * @param \JMS\Serializer\SerializationContext $context
     *
     * @return array|\ArrayObject
     */
    public function serialize(VisitorInterface $visitor, $value, array $type, SerializationContext $context)
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
