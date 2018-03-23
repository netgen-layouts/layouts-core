<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\Value;

final class ItemUpdateStruct extends Value implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;
}
