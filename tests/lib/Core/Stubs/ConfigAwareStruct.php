<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct as APIConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\Value;

final class ConfigAwareStruct extends Value implements APIConfigAwareStruct
{
    use ConfigAwareStructTrait;
}
