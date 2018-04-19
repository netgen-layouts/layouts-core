<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\Config\ConfigDefinitionAwareInterface;
use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;
use Netgen\BlockManager\Value;

final class ConfigDefinitionAware extends Value implements ConfigDefinitionAwareInterface
{
    use ConfigDefinitionAwareTrait;
}
