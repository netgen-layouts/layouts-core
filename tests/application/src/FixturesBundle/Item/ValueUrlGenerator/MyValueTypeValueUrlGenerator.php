<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Bundle\FixturesBundle\Item\ValueUrlGenerator;

use Netgen\BlockManager\Item\ValueUrlGeneratorInterface;

final class MyValueTypeValueUrlGenerator implements ValueUrlGeneratorInterface
{
    /**
     * @param \Netgen\BlockManager\Tests\Bundle\FixturesBundle\Item\Value $object
     */
    public function generate($object): ?string
    {
        return '/value/' . $object->id . '/some/url';
    }
}
