<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue as APIConfigAwareValue;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Value as BaseValue;

final class ConfigAwareValue extends BaseValue implements APIConfigAwareValue
{
    use ConfigAwareValueTrait;
}
