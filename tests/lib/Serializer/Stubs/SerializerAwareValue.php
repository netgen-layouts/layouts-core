<?php

namespace Netgen\BlockManager\Tests\Serializer\Stubs;

use Netgen\BlockManager\Serializer\SerializerAwareTrait;

final class SerializerAwareValue
{
    use SerializerAwareTrait;

    public function getSerializer()
    {
        return $this->serializer;
    }
}
