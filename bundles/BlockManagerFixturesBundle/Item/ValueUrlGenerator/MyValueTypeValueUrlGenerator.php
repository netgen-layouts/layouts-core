<?php

namespace Netgen\Bundle\BlockManagerFixturesBundle\Item\ValueUrlGenerator;

use Netgen\BlockManager\Item\ValueUrlGeneratorInterface;

final class MyValueTypeValueUrlGenerator implements ValueUrlGeneratorInterface
{
    /**
     * @param \Netgen\Bundle\BlockManagerFixturesBundle\Item\Value $object
     *
     * @return null|string
     */
    public function generate($object)
    {
        return '/value/' . $object->id . '/some/url';
    }
}
