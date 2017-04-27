<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue as APIConfigAwareValue;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\ValueObject;

class ConfigAwareValue extends ValueObject implements APIConfigAwareValue
{
    use ConfigAwareValueTrait;
}
