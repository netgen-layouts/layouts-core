<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

interface PluginInterface
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
     * Returns the list of fully qualified class names of the block definition handlers
     * which this plugin extends. If you wish to extend every existing handler,
     * return the array with FQCN of the block handler interface.
     *
     * @return string|string[]
     */
    public static function getExtendedHandler();

    /**
     * Builds the parameters by using provided parameter builder.
     */
    public function buildParameters(ParameterBuilderInterface $builder);

    /**
     * Adds the dynamic parameters to the $params object for the provided block.
     */
    public function getDynamicParameters(DynamicParameters $params, Block $block);
}
