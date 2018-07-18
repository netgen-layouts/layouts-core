<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct as APIConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class ConfigAwareStruct implements APIConfigAwareStruct
{
    use HydratorTrait;
    use ConfigAwareStructTrait;
}
