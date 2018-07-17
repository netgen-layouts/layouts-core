<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;

final class ItemUpdateStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;
}
