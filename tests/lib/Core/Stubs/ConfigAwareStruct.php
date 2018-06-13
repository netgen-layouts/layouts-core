<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct as APIConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\Value as BaseValue;

final class ConfigAwareStruct extends BaseValue implements APIConfigAwareStruct
{
    use ConfigAwareStructTrait;
}
