<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue as APIConfigAwareValue;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Value;

final class ConfigAwareValue extends Value implements APIConfigAwareValue
{
    use ConfigAwareValueTrait;
}
