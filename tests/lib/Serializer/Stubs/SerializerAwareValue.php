<?php

namespace Netgen\BlockManager\Tests\Serializer\Stubs;

use Netgen\BlockManager\Serializer\SerializerAwareTrait;

class SerializerAwareValue
{
    use SerializerAwareTrait;

    /**
     * Returns the request stack.
     *
     * @return \Symfony\Component\Serializer\SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }
}
