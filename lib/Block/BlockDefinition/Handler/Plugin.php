<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

abstract class Plugin implements PluginInterface
{
    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
    }
}
