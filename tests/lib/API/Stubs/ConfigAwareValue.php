<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Stubs;

use Netgen\Layouts\API\Values\Config\ConfigAwareValue as APIConfigAwareValue;
use Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class ConfigAwareValue implements APIConfigAwareValue
{
    use ConfigAwareValueTrait;
    use HydratorTrait;
}
