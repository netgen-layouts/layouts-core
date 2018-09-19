<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

abstract class BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder): void
    {
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
    }

    public function isContextual(Block $block): bool
    {
        return false;
    }
}
