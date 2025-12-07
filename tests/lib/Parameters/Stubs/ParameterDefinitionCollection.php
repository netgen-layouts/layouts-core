<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class ParameterDefinitionCollection implements ParameterDefinitionCollectionInterface
{
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;
}
