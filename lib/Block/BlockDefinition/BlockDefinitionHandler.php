<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;

abstract class BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder): void {}

    public function getDynamicParameters(DynamicParameters $params, Block $block): void {}

    public function isContextual(Block $block): bool
    {
        return false;
    }
}
