<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class ParameterCollection implements ParameterCollectionInterface
{
    use HydratorTrait;
    use ParameterCollectionTrait;
}
