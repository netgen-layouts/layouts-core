<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

/**
 * Block definition handler represents the dynamic/runtime part of the
 * Block definition.
 *
 * Implement this interface to create your own custom blocks.
 */
interface BlockDefinitionHandlerInterface
{
    /**
     * Use this group in the parameters you wish to show
     * in the Content part of the block edit interface.
     */
    const GROUP_CONTENT = 'content';

    /**
     * Use this group in the parameters you wish to show
     * in the Design part of the block edit interface.
     */
    const GROUP_DESIGN = 'design';

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder);

    /**
     * Adds the dynamic parameters to the $params object for the provided block.
     *
     * @param \Netgen\BlockManager\Block\DynamicParameters $params
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function getDynamicParameters(DynamicParameters $params, Block $block);

    /**
     * Returns if the provided block is dependent on a context, i.e. currently displayed page.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isContextual(Block $block);
}
