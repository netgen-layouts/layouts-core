<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Item\ValueUrlGeneratorInterface;

final class ValueUrlGenerator implements ValueUrlGeneratorInterface
{
    public function generate($object)
    {
        return '/item-url';
    }
}
