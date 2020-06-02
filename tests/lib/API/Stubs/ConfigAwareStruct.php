<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Stubs;

use Netgen\Layouts\API\Values\Config\ConfigAwareStruct as APIConfigAwareStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareStructTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class ConfigAwareStruct implements APIConfigAwareStruct
{
    use ConfigAwareStructTrait;
    use HydratorTrait;
}
