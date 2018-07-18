<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\Config\ConfigDefinitionAwareInterface;
use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class ConfigDefinitionAware implements ConfigDefinitionAwareInterface
{
    use HydratorTrait;
    use ConfigDefinitionAwareTrait;
}
