<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Utils\HydratorTrait;

final class CompoundParameterDefinition extends ParameterDefinition implements ParameterDefinitionCollectionInterface
{
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;
}
