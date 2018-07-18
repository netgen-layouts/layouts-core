<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class ParameterCollection implements ParameterCollectionInterface
{
    use HydratorTrait;
    use ParameterCollectionTrait;
}
