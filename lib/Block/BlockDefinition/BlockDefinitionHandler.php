<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

abstract class BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
    }

    public function isContextual(Block $block)
    {
        return false;
    }
}
