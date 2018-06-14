<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Stubs;

use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerAwareValue
{
    use SerializerAwareTrait;

    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }
}
