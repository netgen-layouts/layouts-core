<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue as APIConfigAwareValue;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class ConfigAwareValue implements APIConfigAwareValue
{
    use HydratorTrait;
    use ConfigAwareValueTrait;
}
