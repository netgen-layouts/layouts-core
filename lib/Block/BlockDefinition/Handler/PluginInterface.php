<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Handler;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;

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
     * Returns the list of block definition identifiers which this plugin
     * extends.
     *
     * @return iterable<string>
     */
    // public static function getExtendedIdentifiers(): iterable;

    /**
     * Returns the list of fully qualified class names of the block definition handlers
     * which this plugin extends. If you wish to extend every existing handler,
     * return the list with FQCN of the block handler interface.
     *
     * @return iterable<class-string>
     */
    public static function getExtendedHandlers(): iterable;

    /**
     * Builds the parameters by using provided parameter builder.
     */
    public function buildParameters(ParameterBuilderInterface $builder): void;

    /**
     * Adds the dynamic parameters to the $params object for the provided block.
     */
    public function getDynamicParameters(DynamicParameters $params, Block $block): void;
}
