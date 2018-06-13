<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerFixturesBundle\Item\ValueUrlGenerator;

use Netgen\BlockManager\Item\ValueUrlGeneratorInterface;

final class MyValueTypeValueUrlGenerator implements ValueUrlGeneratorInterface
{
    public function generate($object): ?string
    {
        return '/value/' . $object->id . '/some/url';
    }
}
