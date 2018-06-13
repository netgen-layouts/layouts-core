<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\ValueUrlGenerator;

use Netgen\BlockManager\Item\ValueUrlGeneratorInterface;

final class NullValueUrlGenerator implements ValueUrlGeneratorInterface
{
    public function generate($object)
    {
    }
}
