<?php

namespace Netgen\BlockManager\Tests\Traits\Stubs;

use Netgen\BlockManager\Traits\SerializerAwareTrait;

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
