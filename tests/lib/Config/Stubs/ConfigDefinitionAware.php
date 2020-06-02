<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config\Stubs;

use Netgen\Layouts\Config\ConfigDefinitionAwareInterface;
use Netgen\Layouts\Config\ConfigDefinitionAwareTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class ConfigDefinitionAware implements ConfigDefinitionAwareInterface
{
    use ConfigDefinitionAwareTrait;
    use HydratorTrait;
}
