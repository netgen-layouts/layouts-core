<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\ValueObject;

final class ItemUpdateStruct extends ValueObject implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;
}
