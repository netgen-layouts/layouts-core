<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;

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
    public const GROUP_CONTENT = 'content';

    /**
     * Use this group in the parameters you wish to show
     * in the Design part of the block edit interface.
     */
    public const GROUP_DESIGN = 'design';

    /**
     * Builds the parameters by using provided parameter builder.
     */
    public function buildParameters(ParameterBuilderInterface $builder): void;

    /**
     * Adds the dynamic parameters to the $params object for the provided block.
     */
    public function getDynamicParameters(DynamicParameters $params, Block $block): void;

    /**
     * Returns if the provided block is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(Block $block): bool;
}
